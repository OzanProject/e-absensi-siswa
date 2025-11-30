<?php

namespace App\Http\Controllers\WaliKelas;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Exports\MonthlyRecapExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon; // Pastikan Carbon diimport
use App\Models\ClassModel; // Pastikan ClassModel diimport

class ReportController extends Controller
{
    /**
     * Menampilkan halaman rekap absensi bulanan.
     */
    public function monthlyRecap(Request $request)
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Anda belum mengampu kelas.');
        }

        $classId = $class->id;

        // 1. Ambil Bulan dan Tahun dari Request (Default: Bulan dan Tahun Sekarang)
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $today = Carbon::today(); // 💡 Tanggal Hari Ini untuk Pengecekan Masa Depan

        // 2. Ambil Siswa di Kelas yang Diampu
        $students = Student::where('class_id', $classId)
                           ->orderBy('name', 'asc')
                           ->pluck('name', 'id');
        
        $studentIds = $students->keys();
        
        // 3. Ambil Semua Absensi dalam Periode Bulanan untuk Siswa tersebut
        $absences = Absence::whereIn('student_id', $studentIds)
                           // Query whereBetween harus mencakup seluruh rentang waktu
                           ->whereBetween('attendance_time', [$startOfMonth, $endOfMonth->endOfDay()])
                           ->get();

        // 4. Proses Data menjadi Struktur Pivot (Student ID => [Tanggal => Status])
        $recapData = [];
        $daysInMonth = $startOfMonth->daysInMonth;
        
        // Inisialisasi struktur data
        foreach ($studentIds as $id) {
            $recapData[$id] = [
                'name' => $students[$id],
                'status_by_day' => []
            ];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $currentDay = $startOfMonth->copy()->day($i);
                
                // 💡 PERBAIKAN KUNCI: Pengecekan Masa Depan
                if ($currentDay->isFuture()) {
                    // MASA DEPAN: Tandai sebagai Belum Terjadi
                    $recapData[$id]['status_by_day'][$i] = 'N/A'; 
                } else {
                    // HARI INI atau MASA LALU: Default ke Alpha
                    $recapData[$id]['status_by_day'][$i] = 'Alpha'; 
                }
            }
        }

        // 5. Isi status kehadiran (Loop ini menimpa default 'Alpha'/'N/A' jika record ditemukan)
        foreach ($absences as $absence) {
            $studentId = $absence->student_id;
            $day = $absence->attendance_time->day;
            $status = $absence->status;
            
            // Tentukan status yang paling relevan untuk hari itu
            // Jika sudah ada checkout_time, kita anggap status hari itu adalah status masuknya (Hadir/Terlambat/SIA)
            // Kecuali jika Anda ingin status Pulang tercatat, tapi untuk rekap bulanan, status masuk/keterangan yang utama.
            
            // Langsung timpa, karena ini adalah status yang valid dari database
            $recapData[$studentId]['status_by_day'][$day] = $status;
        }
        
        // 6. Kirim data ke view
        $data = [
            'class' => $class,
            'recapData' => $recapData,
            'daysInMonth' => $daysInMonth,
            'currentMonth' => $startOfMonth->isoFormat('MMMM YYYY'),
            'currentYear' => $year,
            'currentMonthNum' => $month,
        ];
        
        return view('walikelas.report.monthly_recap', $data);
    }

    /**
     * 💡 [FITUR BARU] Proses Export data Rekap Absensi Bulanan ke Excel.
     */
    public function exportMonthlyRecap(Request $request)
    {
        // Panggil kembali logika monthlyRecap untuk mendapatkan data yang sudah diproses
        $response = $this->monthlyRecap($request); 

        // Karena monthlyRecap mengembalikan view, kita harus mendapatkan data dari view data
        if ($response instanceof \Illuminate\View\View) {
            $data = $response->getData();
        } else {
             // Jika ada error (misal, redirect karena tidak mengampu kelas), kembalikan response yang sama
             return $response;
        }
        
        $recapData = $data['recapData'];
        $daysInMonth = $data['daysInMonth'];
        $monthName = $data['currentMonth'];
        $className = $data['class']->name ?? 'Kelas';
        
        // Buat nama file
        $fileName = 'Rekap_Absensi_' . $className . '_' . str_replace(' ', '_', $monthName) . '.xlsx';

        // Panggil Export Class
        return Excel::download(
            new MonthlyRecapExport($recapData, $daysInMonth, $monthName), 
            $fileName
        );
    }
}