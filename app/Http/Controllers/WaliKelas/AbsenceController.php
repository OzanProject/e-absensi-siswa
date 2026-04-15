<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Absence;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AbsenceController extends Controller
{
    // -----------------------------------------------------------------
    // READ & VIEW (Scan Kamera)
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman scan barcode (kamera + log terbaru kelas yang diampu).
     */
    public function scanForm()
    {
        $user = Auth::user();
        // Asumsi relasi user->homeroomTeacher->class tersedia
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')
                ->with('error', 'Anda belum mengampu kelas. Silakan hubungi admin.');
        }

        $classId = $class->id;
        $today = Carbon::today();

        // Ambil log absensi terbaru untuk kelas ini
        $recentAbsences = Absence::with('student.class')
            ->whereDate('attendance_time', $today)
            ->whereHas('student', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->orderBy('attendance_time', 'desc')
            ->take(10)
            ->get();

        // Muat data siswa aktif untuk form manual (jika form manual di-include)
        $students = Student::with('class')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Ambil Setting Lokasi untuk JS Validation
        $settings = Setting::whereIn('key', [
            'school_latitude',
            'school_longitude',
            'school_radius',
            'enable_location_check'
        ])->pluck('value', 'key');

        return view('walikelas.absensi.scan', compact('class', 'students', 'recentAbsences', 'settings'));
    }

    // -----------------------------------------------------------------
    // READ & VIEW (Halaman Manual / Koreksi Data)
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman manajemen Absensi Manual/Koreksi Data Harian.
     */
    public function manualIndex()
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')
                ->with('error', 'Anda belum mengampu kelas.');
        }

        $classId = $class->id;
        $today = Carbon::today();

        // 1. Ambil data siswa aktif untuk dropdown manual
        $students = Student::with('class')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // 2. Ambil semua log absensi hari ini untuk kelas ini (untuk tabel koreksi)
        $todayAttendance = Absence::whereDate('attendance_time', $today)
            ->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->with('student.class')
            ->orderBy('attendance_time', 'desc')
            ->get();

        return view('walikelas.absensi.manual.index', compact('class', 'students', 'todayAttendance'));
    }

    /**
     * Proses pencatatan absensi dari form manual (Sakit/Izin/Alpha/Hadir/Terlambat).
     */
    public function manualStore(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|exists:students,nis',
            'status' => 'required|in:Hadir,Terlambat,Sakit,Izin,Alpha',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Student::where('nis', $request->nis)->first();
        $currentTime = Carbon::now();
        $today = Carbon::today();

        // Cek record absensi hari ini
        $existingAbsence = Absence::where('student_id', $student->id)
            ->whereDate('attendance_time', $today)
            ->exists();

        if ($existingAbsence) {
            return redirect()->back()->with('error', "Status {$student->name} sudah memiliki record absensi hari ini.")->withInput();
        }

        // Catat Absensi Manual
        Absence::create([
            'student_id' => $student->id,
            'attendance_time' => $currentTime,
            'status' => $request->status,
            'notes' => $request->notes,
            'recorded_by' => Auth::check() ? Auth::user()->name : 'Manual',
        ]);

        return redirect()->route('walikelas.absensi.manual.index')->with('success', "Status {$student->name} berhasil dicatat sebagai " . $request->status . '.');
    }

    // -----------------------------------------------------------------
    // CREATE (Record Scan)
    // -----------------------------------------------------------------

    /**
     * Proses pencatatan absensi dari scan barcode (IN/OUT Logic).
     * @param WhatsAppService $waService Service WA (di-inject oleh Laravel)
     */
    public function record(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        $barcode_data = $request->barcode;
        $currentTime = Carbon::now();
        $today = Carbon::today();

        $student = Student::with('class')->where('barcode_data', $barcode_data)->first();

        if (!$student || $student->status !== 'active') {
            $message = $student ? "Siswa {$student->name} non-aktif atau status tidak valid." : 'Siswa tidak ditemukan.';
            return response()->json(['success' => false, 'message' => $message], 404);
        }

        // 🔹 Validasi: Siswa harus berada di kelas yang diampu oleh Wali Kelas login (Kecuali admin)
        // Opsional: Jika Anda ingin Wali Kelas X hanya bisa scan siswa Kelas X, uncomment ini.
        // Tapi biasanya Wali Kelas bisa bantu scan siswa lain jika darurat. 
        // Untuk sekarang kita biarkan terbuka, tapi log recorded_by mencatat nama Wali Kelas.

        $parentPhone = $student->phone_number;

        // 1. Muat Semua Pengaturan Absensi (termasuk Geo & IP)
        // CACHE DURATION REDUCED TO 10 SECONDS FOR DYNAMIC UPDATES
        $settings = Cache::remember('attendance_settings', 10, function () {
            return Setting::whereIn('key', [
                'attendance_start_time',
                'late_tolerance_minutes',
                'attendance_end_time',
                'school_latitude',
                'school_longitude',
                'school_radius',
                'enable_location_check',
                'enable_ip_check',
                'allowed_ip_addresses'
            ])->pluck('value', 'key');
        });

        // Tentukan Jam Pulang yang ditetapkan (Default: 15:00)
        $endTimeSetting = $settings['attendance_end_time'] ?? '15:00';
        $designatedEndTime = Carbon::parse($today->format('Y-m-d') . ' ' . $endTimeSetting);

        // --- VALIDASI GEO & IP (Integrasi Baru) ---
        $ipAddress = $request->ip();
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // A. Validasi IP Address
        if (($settings['enable_ip_check'] ?? 'false') === 'true') {
            $allowedIps = array_map('trim', explode(',', $settings['allowed_ip_addresses'] ?? ''));
            // Tambahkan localhost jika testing
            if (!in_array($ipAddress, $allowedIps) && !in_array($ipAddress, ['127.0.0.1', '::1'])) {
                Log::warning("Wali Kelas Scan Gagal: IP Ditolak.", ['student' => $student->name, 'ip' => $ipAddress]);
                return response()->json(['success' => false, 'message' => 'Akses Ditolak: IP Address Anda tidak dikenali.'], 403);
            }
        }

        // B. Validasi Radius Lokasi
        $distance = 0;
        if (($settings['enable_location_check'] ?? 'false') === 'true') {
            if (!$latitude || !$longitude) {
                return response()->json(['success' => false, 'message' => 'Gagal mendapatkan lokasi GPS. Pastikan GPS aktif.'], 400);
            }

            $schoolLat = $settings['school_latitude'] ?? 0;
            $schoolLng = $settings['school_longitude'] ?? 0;
            $radiusMax = $settings['school_radius'] ?? 100;

            $distance = $this->calculateDistance($latitude, $longitude, $schoolLat, $schoolLng);

            if ($distance > $radiusMax) {
                Log::warning("Wali Kelas Scan Gagal: Diluar Jangkauan.", ['student' => $student->name, 'distance' => $distance]);
                return response()->json([
                    'success' => false,
                    'message' => "Scan Gagal: Anda berada diluar jangkauan sekolah. Jarak: " . round($distance) . " meter (Max: $radiusMax m)."
                ], 403);
            }
        } else {
            // Hitung jarak untuk info saja
            if ($latitude && $longitude) {
                $schoolLat = $settings['school_latitude'] ?? 0;
                $schoolLng = $settings['school_longitude'] ?? 0;
                $distance = $this->calculateDistance($latitude, $longitude, $schoolLat, $schoolLng);
            }
        }


        // --- Cek Record Absensi Hari Ini ---
        $existingAbsence = Absence::where('student_id', $student->id)
            ->whereDate('attendance_time', $today)
            ->first();

        // 2. LOGIC SCAN OUT (PULANG)
        if ($existingAbsence && is_null($existingAbsence->checkout_time)) {

            // 🛑 PENGECEKAN JAM PULANG
            if ($currentTime->lessThan($designatedEndTime)) {
                $timeRemaining = $designatedEndTime->diffForHumans($currentTime, [
                    'parts' => 2,
                    'join' => true,
                    'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE
                ]);

                $message = "❌ Gagal Pulang. Belum waktunya pulang (Jam Pulang: {$endTimeSetting}).";

                return response()->json(['success' => false, 'message' => $message], 409);
            }

            $existingAbsence->checkout_time = $currentTime;
            $existingAbsence->save();

            // Notifikasi WA PULANG
            $this->sendWaNotification($waService, $parentPhone, $student->name, 'PULANG', $currentTime->format('H:i:s'));

            return response()->json([
                'success' => true,
                'message' => 'PULANG: ' . $student->name . ' berhasil dicatat.',
                'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
                'type' => 'OUT',
                'distance' => round($distance)
            ]);

        }

        // 3. Mencegah scan kedua kali
        if ($existingAbsence) {
            $message = $existingAbsence->checkout_time ?
                "Siswa {$student->name} sudah PULANG hari ini." :
                "Siswa {$student->name} sudah Absen MASUK hari ini.";
            return response()->json(['success' => false, 'message' => $message], 409);
        }

        // 4. LOGIC SCAN IN (MASUK/TERLAMBAT)

        $startTimeSetting = $settings['attendance_start_time'] ?? '07:00';
        $toleranceSetting = $settings['late_tolerance_minutes'] ?? 10;

        $defaultStartTime = '07:00';
        $toleranceMinutes = (int) ($toleranceSetting ?: 10);

        $startTime = Carbon::parse($today->format('Y-m-d') . ' ' . ($startTimeSetting ?: $defaultStartTime));
        $toleranceTime = $startTime->copy()->addMinutes($toleranceMinutes);

        $status = 'Hadir';
        $lateDuration = null;

        if ($currentTime->greaterThan($toleranceTime)) {
            $status = 'Terlambat';
            // FIX: Gunakan (int) abs() agar selalu positif dan tanpa desimal
            $lateDuration = (int) abs($startTime->diffInMinutes($currentTime));
        }

        // Catat Absensi Masuk
        Absence::create([
            'student_id' => $student->id,
            'attendance_time' => $currentTime,
            'status' => $status,
            'late_duration' => $lateDuration,
            'recorded_by' => Auth::check() ? Auth::user()->name : 'Wali Kelas',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'ip_address' => $ipAddress,
        ]);

        // Notifikasi WA MASUK/TERLAMBAT — kirim dengan info kelas juga
        $this->sendWaNotification(
            $waService, $parentPhone, $student->name,
            $status, $currentTime->format('H:i'),
            $lateDuration, $student->class->name ?? null
        );

        $message = "{$status}: {$student->name} berhasil dicatat.";
        if ($status === 'Terlambat')
            $message .= " (+{$lateDuration} menit)";

        return response()->json([
            'success' => true,
            'message' => $message,
            'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
            'type' => 'IN',
            'status' => $status,
            'distance' => round($distance)
        ]);
    }

    /**
     * Hitung jarak antara dua koordinat dalam meter (Haversine Formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1 = deg2rad((float) $lat1);
        $lon1 = deg2rad((float) $lon1);
        $lat2 = deg2rad((float) $lat2);
        $lon2 = deg2rad((float) $lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // -----------------------------------------------------------------
    // FUNGSI PENDUKUNG (CRUD TAMBAHAN & WA NOTIFIKASI)
    // -----------------------------------------------------------------

    /**
     * Helper function untuk mengirim WA notification ke nomor orang tua/wali.
     */
    private function sendWaNotification(WhatsAppService $waService, $phone, $studentName, $status, $time, $lateDuration = null, $className = null)
    {
        if (!$phone) {
            Log::warning("No phone number found for student: {$studentName}. Skipping WA notification.");
            return;
        }

        // Ambil nama sekolah dari pengaturan
        $schoolName = Setting::where('key', 'school_name')->value('value') ?? 'Sekolah';
        $today = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
        $classInfo = $className ? "Kelas $className" : '';

        // Format pesan profesional berdasarkan status
        if ($status === 'Hadir') {
            $msg = "✅ *INFORMASI KEHADIRAN SISWA*\n";
            $msg .= "__{$schoolName}__\n";
            $msg .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            $msg .= "Yth. Bapak/Ibu Wali Murid,\n\n";
            $msg .= "Putra/Putri Anda telah *hadir* di sekolah.\n\n";
            $msg .= "👤 *Nama:* {$studentName}\n";
            if ($classInfo) $msg .= "🏫 *{$classInfo}*\n";
            $msg .= "📅 *Tanggal:* {$today}\n";
            $msg .= "⏰ *Jam Masuk:* {$time} WIB\n";
            $msg .= "🟢 *Status:* HADIR\n\n";
            $msg .= "_Terima kasih. Pesan ini dikirim otomatis oleh sistem absensi digital._";

        } elseif ($status === 'Terlambat') {
            $duration = (int) ($lateDuration ?? 0);
            $msg = "⚠️ *PEMBERITAHUAN KETERLAMBATAN*\n";
            $msg .= "__{$schoolName}__\n";
            $msg .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            $msg .= "Yth. Bapak/Ibu Wali Murid,\n\n";
            $msg .= "Putra/Putri Anda tercatat *terlambat* hadir di sekolah.\n\n";
            $msg .= "👤 *Nama:* {$studentName}\n";
            if ($classInfo) $msg .= "🏫 *{$classInfo}*\n";
            $msg .= "📅 *Tanggal:* {$today}\n";
            $msg .= "⏰ *Jam Masuk:* {$time} WIB\n";
            $msg .= "🔴 *Status:* TERLAMBAT (+{$duration} menit)\n\n";
            $msg .= "Mohon menjadi perhatian agar putra/putri Anda dapat hadir tepat waktu.\n\n";
            $msg .= "_Terima kasih. Pesan ini dikirim otomatis oleh sistem absensi digital._";

        } elseif ($status === 'PULANG') {
            $msg = "🏠 *INFORMASI KEPULANGAN SISWA*\n";
            $msg .= "__{$schoolName}__\n";
            $msg .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            $msg .= "Yth. Bapak/Ibu Wali Murid,\n\n";
            $msg .= "Putra/Putri Anda telah *pulang* dari sekolah.\n\n";
            $msg .= "👤 *Nama:* {$studentName}\n";
            if ($classInfo) $msg .= "🏫 *{$classInfo}*\n";
            $msg .= "📅 *Tanggal:* {$today}\n";
            $msg .= "⏰ *Jam Pulang:* {$time} WIB\n";
            $msg .= "🟢 *Status:* PULANG\n\n";
            $msg .= "_Terima kasih. Pesan ini dikirim otomatis oleh sistem absensi digital._";

        } else {
            return; // Lewati jika status lain
        }

        // Kirim via WhatsApp Service
        $waService->sendNotification($phone, $msg);
    }

    /**
     * Hapus record absensi.
     */
    public function destroy(Absence $attendance)
    {
        $studentName = $attendance->student->name ?? 'Siswa';
        $attendance->delete();

        return redirect()->back()->with('success', "Absensi {$studentName} berhasil dihapus.");
    }

    /**
     * Tampilkan form edit absensi (Opsional).
     */
    public function manualEdit(Absence $attendance)
    {
        $students = Student::with('class')->where('status', 'active')->orderBy('name')->get();
        return view('walikelas.absensi.manual.edit', compact('attendance', 'students'));
    }

    /**
     * Update/Edit status absensi manual.
     */
    public function manualUpdate(Request $request, Absence $attendance)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Terlambat,Sakit,Izin,Alpha',
            'notes' => 'nullable|string|max:500',
            'nis' => 'required|string|exists:students,nis',
            // 💡 BARU: Validasi Alasan Koreksi
            'correction_reason' => 'required|string|max:500',
        ]);

        $correctedBy = Auth::check() ? Auth::user()->name : 'System';

        // Update status, notes, dan field audit
        $attendance->update([
            'status' => $request->status,
            'notes' => $request->notes,
            // Jika statusnya non-kehadiran, pastikan checkout_time null (logika lama)
            'checkout_time' => in_array($request->status, ['Sakit', 'Izin', 'Alpha']) ? null : $attendance->checkout_time,

            // 💡 FIELD AUDIT BARU
            'is_manual_corrected' => true,
            'corrected_by' => $correctedBy,
            'correction_note' => $request->correction_reason,
        ]);

        return redirect()->route('walikelas.absensi.manual.index')->with('success', "Status absensi {$attendance->student->name} berhasil diperbarui (Audit Logged).");
    }
    /**
     * 💡 [FITUR BARU] Mengirim notifikasi WhatsApp massal untuk semua siswa yang Absen (Sakit/Izin/Alpha) hari ini.
     * @param WhatsAppService $waService Service WA (di-inject oleh Laravel)
     */
    public function sendDailyAbsenceNotification(WhatsAppService $waService)
    {
        $user = Auth::user();
        $classId = $user->homeroomTeacher->class_id ?? null;

        if (!$classId) {
            return redirect()->back()->with('error', 'Akses ditolak: Anda belum mengampu kelas.');
        }

        $today = Carbon::today();
        $walikelasName = $user->name;
        $class = $user->homeroomTeacher->class->name ?? 'N/A';
        $sentCount = 0;
        $skippedCount = 0;

        Log::info("WA Massal - Wali Kelas: {$walikelasName}, Kelas ID: {$classId}, Tanggal: {$today}");

        // 1. Cari data absensi SIA hari ini untuk kelas ini
        $absencesToNotify = Absence::whereDate('attendance_time', $today)
            ->whereIn('status', ['Sakit', 'Izin', 'Alpha'])
            ->whereHas('student', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->with('student')
            ->get();

        Log::info("WA Massal - Ditemukan {$absencesToNotify->count()} absensi SIA hari ini.");

        if ($absencesToNotify->isEmpty()) {
            // Coba cek apakah ada absensi sama sekali hari ini (untuk debug)
            $anyAbsence = Absence::whereDate('attendance_time', $today)
                ->whereHas('student', fn($q) => $q->where('class_id', $classId))
                ->count();
            Log::info("WA Massal - Total absensi hari ini di kelas: {$anyAbsence}");

            return redirect()->back()->with('warning', 
                "Tidak ada siswa dengan status Sakit, Izin, atau Alpha hari ini di kelas {$class}. " .
                "(Total absensi hari ini: {$anyAbsence} record)"
            );
        }

        // 2. Loop dan Kirim Notifikasi per Siswa
        foreach ($absencesToNotify as $absence) {
            $student = $absence->student;
            $phone = $student->phone_number;

            if ($phone) {
                $status = $absence->status;
                $reason = $absence->notes ? "Keterangan: {$absence->notes}" : '';

                $msg = "🔔 PEMBERITAHUAN KETIDAKHADIRAN 🔔\n\n"
                    . "Yth. Wali Murid {$student->name} (Kelas {$class}),\n\n"
                    . "Anak Anda tercatat *Tidak Hadir* pada hari ini ({$today->isoFormat('D MMMM YYYY')}) dengan status:\n\n"
                    . "Status: *{$status}*\n"
                    . "{$reason}\n\n"
                    . "Pencatat: {$walikelasName}\n"
                    . "Terima kasih.";

                $waService->sendNotification($phone, $msg);
                $sentCount++;
                Log::info("WA Massal - Terkirim ke: {$phone} untuk siswa: {$student->name}");
            } else {
                $skippedCount++;
                Log::warning("WA Massal - Dilewati: Siswa {$student->name} tidak memiliki nomor HP wali.");
            }
        }

        $msg = "✅ Berhasil mengirim {$sentCount} notifikasi WA (Sakit/Izin/Alpha).";
        if ($skippedCount > 0) {
            $msg .= " ⚠️ {$skippedCount} siswa dilewati karena tidak ada nomor HP wali.";
        }

        return redirect()->back()->with('success', $msg);
    }
}