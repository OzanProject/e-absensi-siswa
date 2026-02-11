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
        // 1. DATA ABSENSI HARIAN (Scan Masuk/Pulang)
        $dailyQuery = Absence::with(['student.class'])
            ->whereBetween('attendance_time', [$startDate, $endDate]);

        if ($classId) {
            $dailyQuery->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        $dailyData = $dailyQuery->get();

        // 2. DATA ABSENSI MAPEL (Jurnal Guru)
        $subjectQuery = \App\Models\SubjectAttendance::with(['student.class', 'journal.schedule.subject', 'journal.teacher'])
            ->whereHas('journal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            });

        if ($classId) {
            $subjectQuery->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        $subjectData = $subjectQuery->get();

        // 3. MERGE & NORMALIZE
        $merged = collect();

        // Normalize Daily
        foreach ($dailyData as $item) {
            $obj = new \stdClass();
            $obj->type = 'Harian';
            $obj->student = $item->student;
            $obj->class_name = $item->student->class->name ?? 'N/A';
            $obj->class_grade = $item->student->class->grade ?? 0; // For sorting
            $obj->date = $item->attendance_time;
            $obj->status = $item->status;
            $obj->detail_html = $this->formatDailyDetail($item);
            $obj->raw_data = $item;
            $merged->push($obj);
        }

        // Normalize Subject
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
            $obj->status = ucfirst($item->status); // Ensure Capitalized
            $obj->detail_html = $this->formatSubjectDetail($item);
            $obj->raw_data = $item;
            $merged->push($obj);
        }

        // 4. SORTING (Grade -> Class Name -> Student Name -> Date)
        return $merged->sort(function ($a, $b) {
            // Sort by Grade
            if ($a->class_grade !== $b->class_grade) return $a->class_grade <=> $b->class_grade;
            // Sort by Class Name
            $cmpClass = strcmp($a->class_name, $b->class_name);
            if ($cmpClass !== 0) return $cmpClass;
            // Sort by Student Model
            $cmpName = strcmp($a->student->name ?? '', $b->student->name ?? '');
            if ($cmpName !== 0) return $cmpName;
            // Sort by Date
            return $a->date <=> $b->date;
        });
    }

    private function formatDailyDetail($item)
    {
        $html = '';
        if ($item->status == 'Terlambat') {
            $html .= "<span class='text-amber-600 font-bold text-xs block'>Telat: {$item->late_duration} m</span>";
        }
        if ($item->latitude && $item->longitude) {
            $html .= "<a href='https://maps.google.com/?q={$item->latitude},{$item->longitude}' target='_blank' class='text-[10px] text-blue-500 underline'>Lokasi</a>";
        }
        return $html ?: '-';
    }

    private function formatSubjectDetail($item)
    {
        $subject = $item->journal->schedule->subject->name ?? 'Mapel';
        $teacher = $item->journal->teacher->name ?? 'Guru';
        return "<span class='font-bold text-xs text-indigo-700 block'>{$subject}</span><span class='text-[10px] text-gray-500'>{$teacher}</span>";
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
    /**
     * Menampilkan hasil laporan absensi berdasarkan filter.
     */
    // ...

    /**
     * Helper to get Teacher Report Data
     */
    private function getTeacherReportData(Carbon $startDate, Carbon $endDate)
    {
        // Fetch all teacher attendance within range, ordered by Date DESC, then Teacher Name ASC
        return \App\Models\TeacherAttendance::with('user')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get()
            ->sortBy(function($attendance) {
                return $attendance->user->name ?? '';
            });
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            // class_id required only if type is student (default)
            'class_id' => 'nullable|exists:classes,id', 
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $type = $request->input('type', 'student');

        if ($type === 'teacher') {
            $absences = $this->getTeacherReportData($startDate, $endDate);
            $class = null; // No class context for teachers
            return view('admin.reports.result', compact('absences', 'startDate', 'endDate', 'class', 'type'));
        } else {
            // Student Report
            $classId = $request->class_id;
            $absences = $this->getReportData($startDate, $endDate, $classId);
            $class = $classId ? ClassModel::find($classId) : null;
            return view('admin.reports.result', compact('absences', 'startDate', 'endDate', 'class', 'type'));
        }
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $type = $request->input('type', 'student');

        if ($type === 'teacher') {
            $absences = $this->getTeacherReportData($startDate, $endDate);
            $fileName = "Laporan_Absensi_Guru_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";
            // Pass true for $isAdmin
            return Excel::download(new \App\Exports\TeacherAttendanceExport($absences, true), $fileName);
        } else {
            $classId = $request->class_id;
            $absences = $this->getReportData($startDate, $endDate, $classId);
            $className = $classId ? ClassModel::find($classId)->name : 'Semua-Kelas';
            $fileName = "Laporan_Absensi_{$className}_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";
            return Excel::download(new AbsenceReportExport($absences), $fileName);
        }
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $type = $request->input('type', 'student');

        if ($type === 'teacher') {
            $absences = $this->getTeacherReportData($startDate, $endDate);
            $data = [
                'absences' => $absences,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ];
            $pdf = Pdf::loadView('admin.reports.pdf_teacher', $data);
            $fileName = "Laporan_Absensi_Guru_{$startDate->format('Ymd')}-{$endDate->format('Ymd')}.pdf";
            return $pdf->stream($fileName);
        } else {
            $classId = $request->class_id;
            $absences = $this->getReportData($startDate, $endDate, $classId);
            $class = $classId ? ClassModel::find($classId) : null;

            $data = [
                'absences' => $absences,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'class' => $class,
            ];
            
            $pdf = Pdf::loadView('admin.reports.pdf_template', $data); 
            $fileName = "Laporan_Absensi_" . ($class ? $class->name . "_" : "Semua_Kelas_") . $startDate->format('Ymd') . "-" . $endDate->format('Ymd') . ".pdf";
            return $pdf->stream($fileName);
        }
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