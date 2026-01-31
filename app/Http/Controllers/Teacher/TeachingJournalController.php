<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\TeachingJournal;
use App\Models\SubjectAttendance;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeachingJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $journals = TeachingJournal::where('teacher_id', Auth::id())
            ->with(['schedule.class', 'schedule.subject'])
            ->latest()
            ->paginate(10);

        return view('teacher.journals.index', compact('journals'));
    }

    /**
     * Show the form for creating a new resource (Attendance Sheet).
     */
    public function create(Schedule $schedule)
    {
        // Check if teacher owns this schedule
        if ($schedule->teacher_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Jadwal ini bukan milik Anda.');
        }

        // Prevent creating journal for incorrect day (optional, but good practice)
        Carbon::setLocale('id');
        $todayDay = Carbon::now()->isoFormat('dddd');
        if (strtolower($schedule->day) !== strtolower($todayDay)) {
            // Allow backfilling? Maybe. For now, warn but allow if strictly needed, or block.
            // Let's just flash a warning but allow for demo purposes
            session()->flash('warning', 'Peringatan: Anda mengisi jurnal di luar jadwal hari ini (' . $schedule->day . ').');
        }

        // Check if journal already exists for today
        $existingJournal = TeachingJournal::where('schedule_id', $schedule->id)
            ->where('date', Carbon::today()->toDateString())
            ->first();

        if ($existingJournal) {
            return redirect()->route('teacher.journals.edit', $existingJournal->id)
                ->with('info', 'Jurnal untuk jadwal ini sudah dibuat hari ini. Anda dialihkan ke halaman edit.');
        }

        // Get students in the class
        $students = Student::where('class_id', $schedule->class_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('teacher.journals.create', compact('schedule', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Schedule $schedule)
    {
        // Validation
        $request->validate([
            'topic' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'attendances' => 'required|array',
            'attendances.*' => 'required|in:Hadir,Izin,Sakit,Alpha,Terlambat',
        ]);

        DB::transaction(function () use ($request, $schedule) {
            // 1. Create Journal Header
            $journal = TeachingJournal::create([
                'schedule_id' => $schedule->id,
                'teacher_id' => Auth::id(),
                'date' => Carbon::now()->toDateString(),
                'start_time' => Carbon::now()->toTimeString(),
                'topic' => $request->topic,
                'notes' => $request->notes,
            ]);

            // 2. Create Attendance Details
            foreach ($request->attendances as $studentId => $status) {
                SubjectAttendance::create([
                    'teaching_journal_id' => $journal->id,
                    'student_id' => $studentId,
                    'status' => $status,
                ]);
            }
        });

        return redirect()->route('teacher.dashboard')->with('success', 'Jurnal Mengajar dan Absensi berhasil disimpan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeachingJournal $journal)
    {
        // Authorization
        if ($journal->teacher_id !== Auth::id()) {
            abort(403);
        }

        $schedule = $journal->schedule;
        $attendances = $journal->attendances->pluck('status', 'student_id')->toArray();

        // Get all students again in case new students joined or to ensure complete list
        $students = Student::where('class_id', $schedule->class_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('teacher.journals.edit', compact('journal', 'schedule', 'students', 'attendances'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeachingJournal $journal)
    {
        if ($journal->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'topic' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'attendances' => 'required|array',
            'attendances.*' => 'required|in:Hadir,Izin,Sakit,Alpha,Terlambat',
        ]);

        DB::transaction(function () use ($request, $journal) {
            // Update Header
            $journal->update([
                'topic' => $request->topic,
                'notes' => $request->notes,
                // Optional: update end_time if marking complete
            ]);

            // Update Details
            foreach ($request->attendances as $studentId => $status) {
                SubjectAttendance::updateOrCreate(
                    ['teaching_journal_id' => $journal->id, 'student_id' => $studentId],
                    ['status' => $status]
                );
            }
        });

        return redirect()->route('teacher.dashboard')->with('success', 'Jurnal berhasil diperbarui.');
    }
}
