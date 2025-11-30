<?php

namespace App\Http\Controllers\Parent;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ParentAbsenceExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class ParentController extends Controller
{
    /**
     * Menampilkan dashboard Orang Tua (Fokus pada Statistik/Ringkasan).
     */
    public function index()
    {
        $user = Auth::user();
        $parentRecord = ParentModel::with('students.class')
            ->where('user_id', $user->id)
            ->first();

        if (!$parentRecord) {
            return view('orangtua.dashboard', [
                'user' => $user,
                'parentRecord' => null,
                'totalSIA' => [] 
            ]);
        }

        $studentIds = $parentRecord->students->pluck('id');
        
        // Hitung Statistik Total Absensi SIA (Sejak Awal Semester/Waktu Tertentu)
        $totalSIA = Absence::whereIn('student_id', $studentIds)
                           ->select('status', DB::raw('count(*) as count'))
                           ->whereIn('status', ['Alpha', 'Sakit', 'Izin', 'Terlambat', 'Hadir'])
                           ->groupBy('status')
                           ->pluck('count', 'status')
                           ->toArray();

        // Riwayat absensi tidak dimuat di sini lagi, hanya statistik
        $absences = collect(); // Kirim koleksi kosong agar view tidak error
        $dailyStatus = []; 
        
        return view('orangtua.dashboard', [
            'user' => $user,
            'parentRecord' => $parentRecord,
            'absences' => $absences, // Kosongkan atau pertahankan untuk kompatibilitas view
            'totalSIA' => $totalSIA, 
            'dailyStatus' => $dailyStatus,
        ]);
    }
    
    /**
     * 💡 [FUNGSI BARU] Menampilkan halaman Riwayat Absensi (Tabel 30 hari).
     */
    /**
     * Menampilkan halaman Riwayat Absensi (Tabel 30 hari).
     * Kami akan mempertahankan pagination untuk view.
     */
    public function showAbsenceHistory()
    {
        $user = Auth::user();
        $parentRecord = ParentModel::with('students.class')
            ->where('user_id', $user->id)
            ->first();

        if (!$parentRecord) {
            return redirect()->route('orangtua.dashboard')->with('error', 'Akun belum terhubung ke data siswa.');
        }

        $studentIds = $parentRecord->students->pluck('id');
        
        // Ambil riwayat absensi untuk semua anak (dalam 30 hari terakhir) dengan pagination
        $absences = Absence::with('student.class')
            ->whereIn('student_id', $studentIds)
            ->where('attendance_time', '>=', Carbon::now()->subDays(30))
            ->orderBy('attendance_time', 'desc')
            ->paginate(30); 

        return view('orangtua.report.index', compact('parentRecord', 'absences'));
    }

    /**
     * 💡 [FITUR BARU] Export data Riwayat Absensi ke Excel/PDF.
     */
    public function exportHistory(Request $request, string $format = 'excel')
    {
        $user = Auth::user();
        $parentRecord = ParentModel::where('user_id', $user->id)->first();

        if (!$parentRecord) {
            return redirect()->route('orangtua.dashboard')->with('error', 'Akses Ditolak.');
        }
        
        $studentIds = $parentRecord->students->pluck('id');
        $parentName = $parentRecord->name;
        
        // Ambil SEMUA data tanpa pagination untuk export
        $absencesToExport = Absence::with('student.class')
            ->whereIn('student_id', $studentIds)
            ->where('attendance_time', '>=', Carbon::now()->subDays(30))
            ->orderBy('attendance_time', 'desc')
            ->get(); // Ambil koleksi penuh

        $fileName = 'Riwayat_Absensi_' . str_replace(' ', '_', $parentName) . '_' . Carbon::now()->format('Ymd_His');

        if ($format === 'pdf') {
            // Jika Anda ingin mengimplementasikan PDF, Anda akan menggunakan DomPDF/Snappy di sini
            // Untuk saat ini, kita fokus pada Excel
            return redirect()->back()->with('error', 'Export PDF belum diimplementasikan. Gunakan Excel.');
        }

        // Export ke Excel (XLSX)
        return Excel::download(new ParentAbsenceExport($absencesToExport), $fileName . '.xlsx');
    }

    /**
     * Menampilkan detail satu record absensi, termasuk log audit.
     */
    public function showAbsenceDetail(Absence $absence)
    {
        $user = Auth::user();
        
        // 🛑 OTORISASI KRITIS: Pastikan record absensi ini milik anak dari user yang login
        $parentRecord = ParentModel::where('user_id', $user->id)->first();
        if (!$parentRecord || !$parentRecord->students->pluck('id')->contains($absence->student_id)) {
            abort(403, 'Akses Ditolak. Record absensi ini bukan milik anak Anda.');
        }

        $absence->load('student.class');

        return view('orangtua.absensi.show_detail', compact('absence'));
    }
}