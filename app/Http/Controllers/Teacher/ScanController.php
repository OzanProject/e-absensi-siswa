<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\TeachingJournal;
use App\Models\SubjectAttendance;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ScanController extends Controller
{
    public function index()
    {
        $dayMap = [
            'Sunday' => 'minggu',
            'Monday' => 'senin',
            'Tuesday' => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday' => 'kamis',
            'Friday' => 'jumat',
            'Saturday' => 'sabtu'
        ];
        $today = $dayMap[Carbon::now()->format('l')];

        $schedules = Schedule::where('teacher_id', Auth::id())
            ->where('day', $today)
            ->with(['class', 'subject'])
            ->orderBy('start_time')
            ->get();

        return view('teacher.scan.index', compact('schedules'));
    }

    public function scanner(Schedule $schedule)
    {
        if ($schedule->teacher_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        return view('teacher.scan.scanner', compact('schedule'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'barcode' => 'required|string',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        // --- 1. SETTINGS & LOCATION CHECK ---
        $settings = Cache::remember('attendance_settings', 3600, function () {
            return Setting::whereIn('key', [
                'school_latitude', 'school_longitude', 'school_radius_meters', 'enable_location_check'
            ])->pluck('value', 'key');
        });

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $distance = 0;

        if (($settings['enable_location_check'] ?? 'false') === 'true') {
            if (!$latitude || !$longitude) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendapatkan lokasi. Pastikan GPS aktif.'
                ], 400);
            }

            $schoolLat = $settings['school_latitude'] ?? 0;
            $schoolLng = $settings['school_longitude'] ?? 0;
            $radiusMax = $settings['school_radius_meters'] ?? 100;

            $distance = $this->calculateDistance($latitude, $longitude, $schoolLat, $schoolLng);

            if ($distance > $radiusMax) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Diluar jangkauan. Jarak: " . round($distance) . "m (Max: $radiusMax m)."
                ], 403);
            }
        } else {
             // Calculate distance anyway if coords provided for info
             if ($latitude && $longitude) {
                 $schoolLat = $settings['school_latitude'] ?? 0;
                 $schoolLng = $settings['school_longitude'] ?? 0;
                 $distance = $this->calculateDistance($latitude, $longitude, $schoolLat, $schoolLng);
             }
        }

        // --- 2. STUDENT VALIDATION ---
        
        // Cari siswa by NIS atau NISN
        $student = Student::where('nis', $request->barcode)
            ->orWhere('nisn', $request->barcode)
            ->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data siswa tidak ditemukan!'
            ], 404);
        }

        // Cek apakah siswa anggota kelas yang benar
        if ($student->class_id != $schedule->class_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa ' . $student->name . ' bukan anggota kelas ' . $schedule->class->name
            ], 400);
        }

        // --- 3. JOURNAL & ATTENDANCE ---

        // Cari atau buat Jurnal untuk hari ini
        $journal = TeachingJournal::firstOrCreate(
            [
                'schedule_id' => $schedule->id,
                'date' => Carbon::now()->format('Y-m-d'), 
            ],
            [
                'teacher_id' => Auth::id(),
                'topic' => 'Pertemuan ' . ($schedule->subject->name ?? 'Mapel'),
                'description' => 'Absensi via QR Scan',
                'status' => 'pending' 
            ]
        );

        // Cek jika sudah absen
        $attendance = SubjectAttendance::where('teaching_journal_id', $journal->id)
            ->where('student_id', $student->id)
            ->first();

        if ($attendance && $attendance->status == 'hadir') {
            return response()->json([
                'status' => 'warning',
                'message' => 'Siswa sudah absen sebelumnya.',
                'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
                'type' => 'IN',
                'distance' => round($distance)
            ]);
        }

        // Simpan Absensi
        SubjectAttendance::updateOrCreate(
            [
                'teaching_journal_id' => $journal->id,
                'student_id' => $student->id,
            ],
            [
                'status' => 'hadir' 
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil absen!',
            'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
            'type' => 'IN',
            'distance' => round($distance),
            'time' => Carbon::now()->format('H:i:s')
        ]);
    }

    /**
     * Show Manual Attendance Form
     */
    public function manual(Schedule $schedule)
    {
        if ($schedule->teacher_id != Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // 1. Get or Create Journal for Today (Auto-created if accessing manual)
        $journal = TeachingJournal::firstOrCreate(
            [
                'schedule_id' => $schedule->id,
                'date' => Carbon::now()->format('Y-m-d'),
            ],
            [
                'teacher_id' => Auth::id(),
                'topic' => 'Pertemuan ' . ($schedule->subject->name ?? 'Mapel'),
                'description' => 'Absensi Manual by Guru',
                'status' => 'pending'
            ]
        );

        // 2. Fetch Students
        $students = Student::where('class_id', $schedule->class_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // 3. Fetch Existing Attendance
        $attendances = SubjectAttendance::where('teaching_journal_id', $journal->id)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('teacher.scan.manual', compact('schedule', 'students', 'attendances', 'journal'));
    }

    /**
     * Store Manual Attendance
     */
    public function manualStore(Request $request, Schedule $schedule)
    {
        if ($schedule->teacher_id != Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*' => 'required|in:hadir,izin,sakit,alpha,terlambat', 
        ]);

        // Find Journal (Should exist from view)
        $journal = TeachingJournal::where('schedule_id', $schedule->id)
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->firstOrFail();

        foreach ($request->attendances as $studentId => $status) {
            SubjectAttendance::updateOrCreate(
                [
                    'teaching_journal_id' => $journal->id,
                    'student_id' => $studentId,
                ],
                [
                    'status' => strtolower($status) 
                ]
            );
        }

        return redirect()->route('teacher.scan.index')->with('success', 'Absensi manual berhasil disimpan!');
    }

    /**
     * Hitung jarak (Haversine Formula) - Copied from CentralAbsenceController
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        // Cast to float to avoid Type Error
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
}
