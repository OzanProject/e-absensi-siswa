<?php

namespace App\Http\Controllers\Headmaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Absence;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Helper untuk mengambil data laporan berdasarkan filter.
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

        // Pengurutan standard Laporan
        $query->join('students', 'absences.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->orderBy('classes.grade', 'asc')
            ->orderBy('classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->orderBy('absences.attendance_time', 'asc')
            ->select('absences.*');

        return $query->get();
    }

    /**
     * Tampilkan halaman filter laporan.
     */
    public function index()
    {
        // Ambil semua kelas untuk filter
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        return view('headmaster.report.index', compact('classes'));
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

        return view('headmaster.report.result', compact('absences', 'startDate', 'endDate', 'class'));
    }

    /**
     * Export Excel
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

        $absences = $this->getReportData($startDate, $endDate, $classId);
        $className = $classId ? ClassModel::find($classId)->name : 'Semua-Kelas';
        $fileName = "Laporan_Absensi_KS_{$className}_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";

        return Excel::download(new AbsenceReportExport($absences), $fileName);
    }

    /**
     * Export PDF
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
            'isHeadmaster' => true // Flag untuk view PDF jika perlu pembedaan
        ];

        // Gunakan template PDF yang sama dengan admin
        $pdf = Pdf::loadView('admin.reports.pdf_template', $data);

        $fileName = "Laporan_Absensi_KS_" . ($class ? $class->name . "_" : "Semua_Kelas_") . $startDate->format('Ymd') . "-" . $endDate->format('Ymd') . ".pdf";

        return $pdf->stream($fileName);
    }
}
