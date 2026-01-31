<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TeachingJournal;

use App\Models\ClassModel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = TeachingJournal::where('teacher_id', Auth::id())
            ->with(['schedule.class', 'schedule.subject'])
            ->withCount([
                'attendances as hadir_count' => function ($q) {
                    $q->where('status', 'Hadir');
                }
            ]);

        // Filter Class
        if ($request->filled('class_id')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Filter Date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Default: Sort by latest
        $journals = $query->latest('date')->paginate(10);

        // Data for Filter Dropdown (Only classes taught by this teacher)
        $classes = ClassModel::whereHas('schedules', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->orderBy('grade')->orderBy('name')->get();

        return view('teacher.report.index', compact('journals', 'classes'));
    }
}
