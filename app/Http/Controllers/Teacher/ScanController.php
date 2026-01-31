<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\TeachingJournal;
use App\Models\SubjectAttendance;
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
        if ($schedule->teacher_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        return view('teacher.scan.scanner', compact('schedule'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

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

        // Cari atau buat Jurnal untuk hari ini
        // Kita gunakan lockForUpdate untuk mencegah race condition jika scan cepat
        $journal = TeachingJournal::firstOrCreate(
            [
                'schedule_id' => $schedule->id,
                'date' => Carbon::now()->format('Y-m-d'), // Pastikan format date string
            ],
            [
                'teacher_id' => Auth::id(),
                'title' => 'Pertemuan ' . ($schedule->subject->name ?? 'Mapel'),
                'description' => 'Absensi via QR Scan',
                'status' => 'pending' // Bisa diubah statusnya
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
                'distance' => 0 // Dummy
            ]);
        }

        // Simpan Absensi
        SubjectAttendance::updateOrCreate(
            [
                'teaching_journal_id' => $journal->id,
                'student_id' => $student->id,
            ],
            [
                'status' => 'hadir' // Di mapel biasanya hadir/tidak hadir, terlambat jarang dihitung otomatis by system kecuali manual
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil absen!',
            'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
            'type' => 'IN',
            'distance' => 0, // Dummy
            'time' => Carbon::now()->format('H:i:s')
        ]);
    }
}
