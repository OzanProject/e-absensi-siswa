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
        
        return view('walikelas.absensi.scan', compact('class', 'students', 'recentAbsences')); 
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
        
        $parentPhone = $student->phone_number;
        
        // 1. Muat Semua Pengaturan Absensi (termasuk waktu pulang)
        $settings = Cache::remember('attendance_settings', 60*60, function () {
             return Setting::whereIn('key', ['attendance_start_time', 'late_tolerance_minutes', 'attendance_end_time'])
                            ->pluck('value', 'key');
        });

        // Tentukan Jam Pulang yang ditetapkan (Default: 15:00)
        $endTimeSetting = $settings['attendance_end_time'] ?? '15:00';
        $designatedEndTime = Carbon::parse($today->format('Y-m-d') . ' ' . $endTimeSetting);

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
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                
                $message = "❌ Gagal Pulang. Belum waktunya pulang (Jam Pulang: {$endTimeSetting}). Sisa waktu: {$timeRemaining} lagi.";
                Log::warning("Absensi Gagal: Siswa mencoba pulang sebelum waktunya.", ['student_id' => $student->id, 'current_time' => $currentTime]);
                
                return response()->json(['success' => false, 'message' => $message], 409); 
            }
            // -----------------------------------
            
            $existingAbsence->checkout_time = $currentTime;
            $existingAbsence->save();
            
            // Notifikasi WA PULANG
            $this->sendWaNotification($waService, $parentPhone, $student->name, 'PULANG', $currentTime->format('H:i:s')); 

            return response()->json([
                'success' => true, 
                'message' => $student->name . ' berhasil PULANG pada pukul ' . $currentTime->format('H:i:s') . '.',
                'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
                'type' => 'OUT'
            ]);

        } 
        
        // 3. Mencegah scan kedua kali (sudah masuk, sudah pulang, atau sudah dicatat)
        if ($existingAbsence) {
             $message = $existingAbsence->checkout_time ? 
                         "Siswa {$student->name} sudah PULANG hari ini." : 
                         "Siswa {$student->name} sudah Absen MASUK/dicatat hari ini.";
             return response()->json(['success' => false, 'message' => $message], 409);
        }

        // 4. LOGIC SCAN IN (MASUK/TERLAMBAT)
        
        $startTimeSetting = $settings['attendance_start_time'] ?? '07:00';
        $toleranceSetting = $settings['late_tolerance_minutes'] ?? 10;
        
        $defaultStartTime = '07:00'; 
        $toleranceMinutes = (int)($toleranceSetting ?: 10);
        
        $startTime = Carbon::parse($today->format('Y-m-d') . ' ' . ($startTimeSetting ?: $defaultStartTime));
        $toleranceTime = $startTime->copy()->addMinutes($toleranceMinutes);

        $status = 'Hadir';
        $lateDuration = null;

        if ($currentTime->greaterThan($toleranceTime)) {
             $status = 'Terlambat';
             $lateDuration = $currentTime->diffInMinutes($startTime); 
        }

        // Catat Absensi Masuk
        Absence::create([
             'student_id' => $student->id,
             'attendance_time' => $currentTime,
             'status' => $status,
             'late_duration' => $lateDuration,
             'recorded_by' => Auth::check() ? Auth::user()->name : 'System Scan',
        ]);
        
        // Notifikasi WA MASUK/TERLAMBAT
        $this->sendWaNotification($waService, $parentPhone, $student->name, $status, $currentTime->format('H:i:s'), $lateDuration); 
        
        $message = $student->name . ' berhasil MASUK. Status: ' . $status;
        if ($status === 'Terlambat') {
             $message .= " (+{$lateDuration} menit)";
        }

        return response()->json([
             'success' => true, 
             'message' => $message,
             'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
             'type' => 'IN',
             'status' => $status
        ]);
    }

    // -----------------------------------------------------------------
    // FUNGSI PENDUKUNG (CRUD TAMBAHAN & WA NOTIFIKASI)
    // -----------------------------------------------------------------
    
    /**
     * Helper function untuk mengirim WA notification ke nomor orang tua/wali.
     */
    private function sendWaNotification(WhatsAppService $waService, $phone, $studentName, $status, $time, $lateDuration = null)
    {
        if (!$phone) {
            Log::warning("No phone number found for student: {$studentName}. Skipping WA notification.");
            return;
        }

        // Tentukan pesan berdasarkan status
        if ($status == 'Hadir') {
            $msg = "Anak Anda, {$studentName}, telah berhasil absen MASUK pada pukul {$time}. Status: HADIR.";
        } elseif ($status == 'Terlambat') {
            $duration = $lateDuration ?? 0;
            $msg = "⚠️ Anak Anda, {$studentName}, absen MASUK TERLAMBAT pada pukul {$time}. Keterlambatan: {$duration} menit.";
        } elseif ($status == 'PULANG') {
            $msg = "Anak Anda, {$studentName}, telah absen PULANG pada pukul {$time}.";
        } else {
            return; // Lewati jika status Sakit/Izin/Alpha
        }
        
        // Panggil service untuk mengirim pesan
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

        // 1. Ambil semua catatan absensi hari ini yang berstatus SIA (Sakit, Izin, Alpha)
        $absencesToNotify = Absence::whereDate('attendance_time', $today)
            ->whereIn('status', ['Sakit', 'Izin', 'Alpha'])
            ->whereHas('student', function ($query) use ($classId) {
                // Batasan Kritis: Hanya siswa di kelas Wali Kelas ini
                $query->where('class_id', $classId); 
            })
            ->with('student')
            ->get();
            
        if ($absencesToNotify->isEmpty()) {
             return redirect()->back()->with('warning', 'Tidak ada siswa dengan status Sakit, Izin, atau Alpha hari ini di kelas Anda.');
        }

        // 2. Loop dan Kirim Notifikasi per Siswa
        foreach ($absencesToNotify as $absence) {
            $student = $absence->student;
            $phone = $student->phone_number;

            // Pastikan nomor HP tersedia
            if ($phone) {
                $status = $absence->status;
                $reason = $absence->notes ? "Keterangan: {$absence->notes}" : '';
                
                $msg = "🔔 PEMBERITAHUAN KETIDAKHADIRAN 🔔\n\n"
                     . "Yth. Wali Murid {$student->name} (Kelas {$class}),\n\n"
                     . "Anak Anda tercatat **Absen** pada hari ini ({$today->isoFormat('D MMMM YYYY')}) dengan status:\n\n"
                     . "Status: *{$status}*\n"
                     . "{$reason}\n\n"
                     . "Pencatat: {$walikelasName}\n"
                     . "Terima kasih.";

                // Panggil Service WA (Asumsi sendNotification menerima $phone dan $message)
                $waService->sendNotification($phone, $msg);
                $sentCount++;
            } else {
                Log::warning("WA Notif Gagal: Siswa {$student->name} tidak memiliki nomor HP wali.");
            }
        }
        
        return redirect()->back()->with('success', "✅ Berhasil mengirim {$sentCount} notifikasi ketidakhadiran (Sakit/Izin/Alpha) via WhatsApp.");
    }
}