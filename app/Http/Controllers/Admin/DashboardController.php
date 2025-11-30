<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Absence;
use App\Models\HomeroomTeacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Super Admin dengan statistik utama.
     */
    public function index()
    {
        $today = Carbon::today();
        
        // --- 1. STATISTIK DATA MASTER & PENGGUNA ---
        
        // Total Kelas Aktif
        $totalClasses = ClassModel::where('status', 'active')->count();
        
        // Total Siswa Aktif
        $totalStudents = Student::where('status', 'active')->count();
        
        // Total Guru/Wali Kelas (Berdasarkan role: wali_kelas atau guru)
        $totalTeachers = User::whereIn('role', ['wali_kelas', 'guru'])->count();
        
        // Total Semua Akun Pengguna di Sistem
        $totalUsers = User::count();

        // ✅ TAMBAHAN: Total Akun Menunggu Persetujuan
        $pendingUsers = User::where('is_approved', false)
                            ->where('role', '!=', 'super_admin') // Tidak menghitung super admin
                            ->count();
        
        // --- 2. STATISTIK ABSENSI HARI INI ---
        
        // Hitung siswa unik yang hadir/terlambat hari ini
        $presentToday = Absence::whereDate('attendance_time', $today)
                            ->whereIn('status', ['Hadir', 'Terlambat'])
                            ->distinct('student_id')
                            ->count('student_id');

        // Hitung persentase kehadiran
        $attendancePercentage = 0;
        if ($totalStudents > 0) {
            $attendancePercentage = round(($presentToday / $totalStudents) * 100, 1);
        }
        
        // --- 3. LOG ABSENSI TERBARU (Global) ---
        $recentAbsences = Absence::with(['student.class'])
                            ->whereDate('attendance_time', $today)
                            ->orderBy('attendance_time', 'desc')
                            ->take(10)
                            ->get();
        
        // --- 4. INFO SISTEM ---
        $phpVersion = PHP_VERSION;
        
        // Mengirimkan semua data
        return view('admin.dashboard', compact(
            'totalClasses', 
            'totalStudents', 
            'attendancePercentage', 
            'totalTeachers',
            'recentAbsences',
            'totalUsers',
            'pendingUsers', // ✅ Variabel baru
            'phpVersion'
        ));
    }
}