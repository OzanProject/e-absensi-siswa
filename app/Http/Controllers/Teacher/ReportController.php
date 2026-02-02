<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubjectAttendance;
use App\Models\ClassModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Get Normalized Report Data (Subject Attendance Only)
     */
    private function getReportData(Carbon $startDate, Carbon $endDate, $classId = null)
    {
        $teacherId = Auth::id();

        // Query Subject Attendance linked to this Teacher's Journals
        $query = SubjectAttendance::with(['student.class', 'journal.schedule.subject', 'journal.teacher'])
            ->whereHas('journal', function ($q) use ($startDate, $endDate, $teacherId) {
                $q->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->where('teacher_id', $teacherId);
            });

        if ($classId) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
        }

        $subjectData = $query->get();

        // Normalize to Standard Object Structure
        $merged = collect();

        foreach ($subjectData as $item) {
            $obj = new \stdClass();
            $obj->type = 'Mapel';
            $obj->student = $item->student;
            $obj->class_name = $item->student->class->name ?? 'N/A';
            $obj->class_grade = $item->student->class->grade ?? 0;
            
            // Fix Carbon formatting issue - ensure date is string 'Y-m-d'
            $dateStr = $item->journal->date instanceof \DateTimeInterface 
                ? $item->journal->date->format('Y-m-d') 
                : substr($item->journal->date, 0, 10);
            $timeStr = $item->journal->start_time ?? '00:00:00';
            
            $obj->date = Carbon::parse($dateStr . ' ' . $timeStr);
            $obj->status = ucfirst($item->status);
            $obj->detail_html = $this->formatSubjectDetail($item);
            $obj->raw_data = $item;
    
            $merged->push($obj);
        }

        // Sort: Class -> Student -> Date
        return $merged->sort(function ($a, $b) {
            $cmpClass = strcmp($a->class_name, $b->class_name);
            if ($cmpClass !== 0) return $cmpClass;
            
            $cmpName = strcmp($a->student->name ?? '', $b->student->name ?? '');
            if ($cmpName !== 0) return $cmpName;
            
            return $a->date <=> $b->date;
        });
    }

    private function formatSubjectDetail($item)
    {
        $subject = $item->journal->schedule->subject->name ?? 'Mapel';
        // No need to show teacher name since it's the logged-in teacher report
        return "<span class='font-bold text-xs text-indigo-700 block'>{$subject}</span>";
    }

    public function index(Request $request)
    {
        // Default: Today if not set, or preserve range
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
        
        $classId = $request->class_id;

        // Get Data
        $absences = $this->getReportData($startDate, $endDate, $classId);

        // Filter Classes (Only classes taught by this teacher)
        $classes = ClassModel::whereHas('schedules', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->orderBy('grade')->orderBy('name')->get();

        return view('teacher.report.index', compact('absences', 'classes', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $classId = $request->class_id;

        $absences = $this->getReportData($startDate, $endDate, $classId);
        $className = $classId ? ClassModel::find($classId)->name : 'Semua-Kelas';
        $fileName = "Laporan_Absensi_Guru_{$className}_{$startDate->format('Ymd')}.xlsx";

        return Excel::download(new AbsenceReportExport($absences), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $classId = $request->class_id;

        $absences = $this->getReportData($startDate, $endDate, $classId);
        $class = $classId ? ClassModel::find($classId) : null;

        $data = [
            'absences' => $absences,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'class' => $class,
            'isHeadmaster' => false // Reuse admin template
        ];

        $pdf = Pdf::loadView('admin.reports.pdf_template', $data);
        return $pdf->stream('Laporan_Absensi_Guru.pdf');
    }
}
