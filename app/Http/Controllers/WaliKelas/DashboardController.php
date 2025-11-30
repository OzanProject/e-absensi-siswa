<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Absence;
use App\Models\Student;
use App\Models\HomeroomTeacher;
use App\Models\IzinRequest; // 💡 Import Model IzinRequest
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $homeroomTeacher = $user->homeroomTeacher ?? null;
        $class = $homeroomTeacher->class ?? null; 

        // Inisialisasi variabel statistik
        $classId = $class ? $class->id : null; 
        $totalStudents = 0;
        $presentToday = 0;
        $absentToday = 0;
        $recentAbsences = collect(); 
        $warningStudents = collect();
        $dailyStats = []; 
        $pendingRequestsCount = 0; // 💡 Inisialisasi counter Izin Pending
        
        $maxAlpha = 3;
        $maxSick = 5;
        $maxIzin = 5;


        if ($classId) {
            $today = Carbon::today();
            $totalStudents = Student::where('class_id', $classId)->count();

            // 1. Ambil Semua Settings (termasuk batas absensi)
            $settings = Cache::remember('max_absensi_settings_full', 60*60, function () {
                return Setting::whereIn('key', ['attendance_start_time', 'max_alpha', 'max_sick', 'max_izin'])
                               ->pluck('value', 'key');
            });
            
            // Tentukan batas absensi dan pastikan tipe integer
            $maxAlpha = (int)($settings['max_alpha'] ?? 3);
            $maxSick = (int)($settings['max_sick'] ?? 5);
            $maxIzin = (int)($settings['max_izin'] ?? 5);


            // 2. Hitung Total Absensi SIA (Untuk Peringatan)
            $currentCounts = Absence::select('student_id', 'status', DB::raw('count(*) as count'))
                ->whereIn('status', ['Alpha', 'Sakit', 'Izin'])
                ->whereHas('student', function ($query) use ($classId) {
                    $query->where('class_id', $classId);
                })
                ->groupBy('student_id', 'status')
                ->get();

            // 3. Proses Peringatan
            foreach ($currentCounts as $count) {
                $maxLimit = 0;
                $warningType = '';

                if ($count->status == 'Alpha') {
                    $maxLimit = $maxAlpha;
                    $warningType = 'Alpha';
                } elseif ($count->status == 'Sakit') {
                    $maxLimit = $maxSick;
                    $warningType = 'Sakit';
                } elseif ($count->status == 'Izin') {
                    $maxLimit = $maxIzin;
                    $warningType = 'Izin';
                }

                if ($count->count >= $maxLimit && $maxLimit > 0) {
                    if (!$warningStudents->pluck('student_id')->contains($count->student_id)) {
                        $student = Student::find($count->student_id);
                        if ($student) {
                            $warningStudents->push([
                                'student_id' => $student->id,
                                'name' => $student->name,
                                'class_name' => $class->name,
                                'warning_status' => $warningType,
                                'count' => $count->count,
                                'max_limit' => $maxLimit
                            ]);
                        }
                    }
                }
            }
            
            // 4. Hitung Statistik Harian per Status
            $dailyStats = Absence::whereDate('attendance_time', $today)
                                  ->whereHas('student', function ($q) use ($classId) {
                                      $q->where('class_id', $classId);
                                  })
                                  ->select('status', DB::raw('count(DISTINCT student_id) as count'))
                                  ->groupBy('status')
                                  ->pluck('count', 'status')
                                  ->toArray();


            // 5. Hitung Hadir dan Sisa Siswa
            $presentToday = ($dailyStats['Hadir'] ?? 0) + ($dailyStats['Terlambat'] ?? 0);
            
            $recordedStudentsCount = Absence::whereDate('attendance_time', $today)
                                            ->whereHas('student', function ($q) use ($classId) {
                                                $q->where('class_id', $classId);
                                            })
                                            ->distinct('student_id')
                                            ->count('student_id');
                                            
            $absentToday = $totalStudents - $recordedStudentsCount;


            // 6. Log Absensi Terbaru (tetap sama)
            $recentAbsences = Absence::with('student.class')
                                     ->whereDate('attendance_time', $today)
                                     ->whereHas('student', function ($query) use ($classId) {
                                         $query->where('class_id', $classId);
                                     })
                                     ->orderBy('attendance_time', 'desc')
                                     ->take(5)
                                     ->get();
                                     
            // 💡 BARU: Hitung Permintaan Izin Pending
            $pendingRequestsCount = IzinRequest::where('status', 'Pending')
                                               ->whereHas('student', function ($query) use ($classId) {
                                                   $query->where('class_id', $classId);
                                               })
                                               ->count();
        }
        
        // Mengirimkan semua data
        return view('walikelas.dashboard', [
            'user' => $user, 
            'class' => $class, 
            'totalStudents' => $totalStudents, 
            'presentToday' => $presentToday, 
            'absentToday' => $absentToday, 
            'recentAbsences' => $recentAbsences,
            'warningStudents' => $warningStudents, 
            'dailyStats' => $dailyStats,
            'maxLimits' => ['Alpha' => $maxAlpha, 'Sakit' => $maxSick, 'Izin' => $maxIzin],
            'pendingRequestsCount' => $pendingRequestsCount // 💡 Kirim Count ke View
        ]);
    }
}