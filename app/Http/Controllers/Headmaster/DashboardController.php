<?php

namespace App\Http\Controllers\Headmaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\TeachingJournal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Ringkasan Utama
        $totalClasses = ClassModel::count();
        $totalStudents = Student::where('status', 'active')->count();
        $totalTeachers = User::where('role', 'guru')->where('is_approved', true)->count();

        $today = Carbon::today()->toDateString();

        // 2. Statistik Kehadiran Hari Ini (Global)
        $presentToday = DB::table('subject_attendances')
            ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
            ->where('teaching_journals.date', $today)
            ->where('subject_attendances.status', 'Hadir')
            ->count();

        // 3. Jurnal Guru Hari Ini
        $journalsToday = TeachingJournal::where('date', $today)->count();
        $schedulesToday = Schedule::where('day', Carbon::now()->isoFormat('dddd'))->count();
        $journalPercentage = $schedulesToday > 0 ? round(($journalsToday / $schedulesToday) * 100) : 0;

        // 4. [NEW] Early Warning: Kelas dengan Kehadiran < 75% Hari Ini
        $lowAttendanceClasses = [];
        $classes = ClassModel::all();
        foreach ($classes as $c) {
            // Hitung kehadiran per kelas hari ini
            $totalEntries = DB::table('subject_attendances')
                ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
                ->join('schedules', 'teaching_journals.schedule_id', '=', 'schedules.id')
                ->where('schedules.class_id', $c->id)
                ->where('teaching_journals.date', $today)
                ->count();

            if ($totalEntries > 0) {
                $presents = DB::table('subject_attendances')
                    ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
                    ->join('schedules', 'teaching_journals.schedule_id', '=', 'schedules.id')
                    ->where('schedules.class_id', $c->id)
                    ->where('teaching_journals.date', $today)
                    ->where('subject_attendances.status', 'Hadir')
                    ->count();

                $rate = round(($presents / $totalEntries) * 100);
                if ($rate < 75) {
                    $lowAttendanceClasses[] = [
                        'name' => $c->name,
                        'rate' => $rate,
                        'homeroom' => $c->homeroomTeacher ? ($c->homeroomTeacher->user->name ?? '-') : '-'
                    ];
                }
            }
        }

        // 5. [NEW] Tren Kehadiran Siswa (Grafik Bulanan Tahun Ini)
        $monthlyAttendance = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::create(null, $m, 1)->startOfMonth()->format('Y-m-d');
            $monthEnd = Carbon::create(null, $m, 1)->endOfMonth()->format('Y-m-d');

            $totalMonth = DB::table('subject_attendances')
                ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
                ->whereBetween('teaching_journals.date', [$monthStart, $monthEnd])
                ->count();

            $presentMonth = DB::table('subject_attendances')
                ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
                ->whereBetween('teaching_journals.date', [$monthStart, $monthEnd])
                ->where('subject_attendances.status', 'Hadir')
                ->count();

            $rate = $totalMonth > 0 ? round(($presentMonth / $totalMonth) * 100, 1) : 0;
            $monthlyAttendance[] = $rate;
        }

        // 6. [NEW] Analitik Kinerja Guru (Berdasarkan Pengisian Jurnal Bulan Ini)
        // Hitung persentase jurnal vs jadwal bulan ini per guru
        $currentMonth = Carbon::now()->format('Y-m');

        // Ambil guru beserta jumlah jadwal mengajarnya (sebagai konteks beban kerja)
        $teachers = User::where('role', 'guru')
            ->where('is_approved', true)
            ->withCount('teachingSchedules')
            ->get();

        $teacherPerformance = [];

        foreach ($teachers as $guru) {
            // Hitung jurnal yang diisi bulan ini
            $journalsCount = TeachingJournal::where('teacher_id', $guru->id)
                ->where('date', 'like', "$currentMonth%")
                ->count();

            $teacherPerformance[] = [
                'name' => $guru->name,
                'count' => $journalsCount,
                'schedules_count' => $guru->teaching_schedules_count // Menambahkan konteks beban mengajar
            ];
        }

        // Sort: Rajin (Top) ke Kurang (Bottom)
        usort($teacherPerformance, function ($a, $b) {
            // Prioritas 1: Jumlah Jurnal (Desc)
            if ($b['count'] !== $a['count']) {
                return $b['count'] <=> $a['count'];
            }
            // Prioritas 2: Beban Mengajar (Desc) - Jika jurnal sama, yang beban lebih banyak di atas
            return $b['schedules_count'] <=> $a['schedules_count'];
        });

        // Logika Top 5 & Bottom 5 (Tanpa Overlap)
        $totalListed = count($teacherPerformance);

        if ($totalListed <= 5) {
            // Jika guru sedikit (< 5), tampilkan semua di Top, Bottom kosong
            $topTeachers = $teacherPerformance;
            $bottomTeachers = [];
        } else {
            $topTeachers = array_slice($teacherPerformance, 0, 5);

            // Bottom 5 diambil dari bawah, tapi pastikan tidak overlap dengan Top 5
            // Contoh: 7 guru. Top 5 (0-4). Bottom 5 (2-6). Overlap di 2,3,4.
            // Solusi: Ambil 5 terbawah, lalu filter yang tidak ada di Top 5.
            $rawBottom = array_slice($teacherPerformance, -5);

            // Filter bottom agar tidak menampilkan nama yang sudah ada di top (kasus 6-9 guru)
            $topNames = array_column($topTeachers, 'name');
            $bottomTeachers = array_filter($rawBottom, function ($item) use ($topNames) {
                return !in_array($item['name'], $topNames);
            });

            // Urutkan bottom dari yang terendah ke tinggi (agar yang paling malas di atas)
            usort($bottomTeachers, function ($a, $b) {
                return $a['count'] <=> $b['count'];
            });
        }

        // 7. [NEW] Analitik Kedisiplinan Siswa (Bulan Ini)
        // A. Siswa Sering Terlambat (Absensi Harian)
        $topLateStudents = DB::table('absences')
            ->join('students', 'absences.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereYear('absences.attendance_time', Carbon::now()->year)
            ->whereMonth('absences.attendance_time', Carbon::now()->month)
            ->where('absences.status', 'Terlambat')
            ->select('students.name', 'classes.name as class_name', DB::raw('count(*) as total'))
            ->groupBy('students.id', 'students.name', 'classes.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // B. Siswa Sering Alpha (Jurnal Subject)
        $topAlphaStudents = DB::table('subject_attendances')
            ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
            ->join('students', 'subject_attendances.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->where('teaching_journals.date', 'like', "$currentMonth%")
            ->where('subject_attendances.status', 'Alpha')
            ->select('students.name', 'classes.name as class_name', DB::raw('count(*) as total'))
            ->groupBy('students.id', 'students.name', 'classes.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('headmaster.dashboard', compact(
            'totalClasses',
            'totalStudents',
            'totalTeachers',
            'presentToday',
            'journalsToday',
            'schedulesToday',
            'journalPercentage',
            'lowAttendanceClasses',
            'monthlyAttendance',
            'topTeachers',
            'bottomTeachers',
            'topLateStudents',
            'topAlphaStudents'
        ));
    }

    public function report(Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));

        // Ambil semua kelas
        $classes = ClassModel::with(['homeroomTeacher.user'])->orderBy('grade')->orderBy('name')->get();

        // Siapkan data rekap per kelas
        $reportData = [];

        foreach ($classes as $class) {
            $studentIds = $class->students()->pluck('id');

            // Hitung total sesi pelajaran di bulan ini untuk kelas ini
            $totalJournals = TeachingJournal::whereHas('schedule', function ($q) use ($class) {
                $q->where('class_id', $class->id);
            })->where('date', 'like', "$selectedMonth%")->count();

            // Hitung rata-rata kehadiran
            $attendanceStats = DB::table('subject_attendances')
                ->join('teaching_journals', 'subject_attendances.teaching_journal_id', '=', 'teaching_journals.id')
                ->join('schedules', 'teaching_journals.schedule_id', '=', 'schedules.id')
                ->where('schedules.class_id', $class->id)
                ->where('teaching_journals.date', 'like', "$selectedMonth%")
                ->select('subject_attendances.status', DB::raw('count(*) as total'))
                ->groupBy('subject_attendances.status')
                ->pluck('total', 'status')
                ->toArray();

            $hadir = $attendanceStats['Hadir'] ?? 0;
            $sakit = $attendanceStats['Sakit'] ?? 0;
            $izin = $attendanceStats['Izin'] ?? 0;
            $alpha = $attendanceStats['Alpha'] ?? 0;
            $totalEntries = $hadir + $sakit + $izin + $alpha;

            $presenceRate = $totalEntries > 0 ? round(($hadir / $totalEntries) * 100, 1) : 0;

            $reportData[] = [
                'class' => $class,
                'total_journals' => $totalJournals,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'rate' => $presenceRate
            ];
        }

        // Sort by Presence Rate (Low to High) untuk highlight masalah
        usort($reportData, function ($a, $b) {
            return $a['rate'] <=> $b['rate'];
        });

        return view('headmaster.report.recap', compact('reportData', 'selectedMonth'));
    }

    public function exportExcel(Request $request)
    {
        // TODO: Implement export excel logic utilizing existing exports or creating new one
        return redirect()->back()->with('error', 'Fitur Export Excel akan segera hadir.');
    }
}
