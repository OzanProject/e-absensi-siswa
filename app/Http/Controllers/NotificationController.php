<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil daftar notifikasi absensi terbaru untuk user yang login.
     * Endpoint ini akan dipanggil oleh AJAX setiap beberapa detik.
     */
    public function getLatestNotifications()
    {
        $user = Auth::user();
        $notifications = [];
        $limit = 5;

        // Implementasi sederhana: Menampilkan 5 aktivitas absensi terbaru di sistem
        // Untuk sistem notifikasi yang sebenarnya (perlu tabel notifikasi khusus), logic-nya lebih kompleks.

        $absences = Absence::with('student.class')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        // Tentukan Base URL berdasarkan Role
        $baseUrl = match ($user->role) {
            'super_admin' => route('report.index'),
            'kepala_sekolah' => route('headmaster.report.index'),
            'wali_kelas' => route('walikelas.report.index'),
            'guru' => route('teacher.report.index'),
            // Tambahkan role lain jika perlu
            default => '#'
        };

        foreach ($absences as $absence) {
            $notifications[] = [
                'icon' => $absence->status === 'Terlambat' ? 'fas fa-exclamation-triangle text-warning' : 'fas fa-user-check text-success',
                'title' => $absence->student->name . ' - ' . $absence->status,
                'time' => $absence->created_at->diffForHumans(),
                'status' => $absence->status,
                // Tambahkan URL spesifik
                'url' => $baseUrl,
            ];
        }

        return response()->json([
            'count' => count($notifications),
            'notifications' => $notifications
        ]);
    }
}