<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\TeachingJournal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure locale is ID for day matching
        Carbon::setLocale('id');
        $todayDay = Carbon::now()->isoFormat('dddd'); // Senin, Selasa...

        // Get schedules for today where the logged in user is the teacher
        $schedules = Schedule::where('teacher_id', $user->id)
            ->where('day', $todayDay)
            ->with(['class', 'subject'])
            ->orderBy('start_time')
            ->get();

        // Enrich schedules with journal status
        $schedules->transform(function ($schedule) {
            $journal = TeachingJournal::where('schedule_id', $schedule->id)
                ->where('date', Carbon::today()->toDateString())
                ->first();
            $schedule->journal_status = $journal ? 'filled' : 'pending';
            $schedule->journal_id = $journal ? $journal->id : null;
            return $schedule;
        });

        // Calculate statistics
        $stats = [
            'total_classes' => $schedules->count(),
            'filled' => $schedules->where('journal_status', 'filled')->count(),
            'pending' => $schedules->where('journal_status', 'pending')->count(),
        ];

        return view('teacher.dashboard', compact('schedules', 'stats', 'todayDay'));
    }
}
