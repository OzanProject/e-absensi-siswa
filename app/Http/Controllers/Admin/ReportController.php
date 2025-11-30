<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\ClassModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceReportExport; 
use Illuminate\Support\Facades\DB; 

class ReportController extends Controller
{
    /**
     * Helper untuk mengambil data laporan berdasarkan filter.
     * Mengurutkan berdasarkan Tingkat Kelas, Nama Kelas, dan Nama Siswa.
     */
    private function getReportData(Carbon $startDate, Carbon $endDate, $classId = null)
    {
        $query = Absence::with(['student.class'])
            ->whereBetween('attendance_time', [$startDate, $endDate]); 

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        
        // 💡 PENGURUTAN KUNCI: Menggunakan JOIN untuk OrderBy Relasi
        $query->join('students', 'absences.student_id', '=', 'students.id')
              ->join('classes', 'students.class_id', '=', 'classes.id')
              ->orderBy('classes.grade', 'asc') // Urutkan Tingkat (7, 8, 9)
              ->orderBy('classes.name', 'asc')  // Urutkan Kelas (7A, 7B)
              ->orderBy('students.name', 'asc') // Urutkan Nama Siswa di dalam Kelas
              ->orderBy('absences.attendance_time', 'asc') // Kemudian waktu absensi
              ->select('absences.*'); // Penting: Pilih kembali semua kolom dari tabel absences
              
        return $query->get();
    }
    
    // -----------------------------------------------------------------
    // SUPER ADMIN REPORTS
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman filter laporan. (Super Admin)
     */
    public function index()
    {
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get(); 
        return view('admin.reports.index', compact('classes'));
    }

    /**
     * Menampilkan hasil laporan absensi berdasarkan filter.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classId = $request->class_id;

        $absences = $this->getReportData($startDate, $endDate, $classId);
        $class = $classId ? ClassModel::find($classId) : null;
        
        return view('admin.reports.result', compact('absences', 'startDate', 'endDate', 'class'));
    }

    /**
     * Export laporan ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:classes,id',
        ]);
        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classId = $request->class_id;
        
        // Data absensi diambil menggunakan helper
        $absences = $this->getReportData($startDate, $endDate, $classId);
        
        $className = $classId ? ClassModel::find($classId)->name : 'Semua Kelas';

        $fileName = "Laporan_Absensi_{$className}_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";

        // 💡 Perbaikan Panggil Export Class: Mengirim koleksi data yang sudah di-query.
        return Excel::download(new AbsenceReportExport($absences, $startDate, $endDate), $fileName);
    }

    /**
     * Export laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classId = $request->class_id;
        
        $absences = $this->getReportData($startDate, $endDate, $classId);
        $class = $classId ? ClassModel::find($classId) : null;

        $data = [
            'absences' => $absences,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'class' => $class,
        ];
        
        // Render PDF menggunakan Facade
        $pdf = Pdf::loadView('admin.reports.pdf_template', $data); 
        
        $fileName = "Laporan_Absensi_PDF_" . Carbon::now()->format('YmdHis') . ".pdf";
        
        return $pdf->stream($fileName);
    }

    // -----------------------------------------------------------------
    // WALI KELAS REPORTS
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman filter laporan absensi untuk Wali Kelas.
     */
    public function walikelasIndex()
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null; 

        if (!$class) {
             return redirect()->route('walikelas.dashboard')
                              ->with('error', 'Anda belum mengampu kelas. Silakan hubungi admin untuk pengaturan.');
        }

        return view('walikelas.reports.index', compact('class')); 
    }

    /**
     * Menampilkan hasil laporan absensi untuk Wali Kelas.
     */
    public function walikelasGenerate(Request $request)
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')->with('error', 'Anda belum mengampu kelas.');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classId = $class->id; // Filter kelas otomatis

        $absences = $this->getReportData($startDate, $endDate, $classId);
        
        return view('walikelas.reports.result', compact('absences', 'startDate', 'endDate', 'class'));
    }
}