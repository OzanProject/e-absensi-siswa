<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar kelas untuk memilih jadwal.
     */
    public function index()
    {
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        return view('admin.schedules.index', compact('classes'));
    }

    /**
     * Menampilkan jadwal untuk kelas tertentu.
     */
    public function show($id)
    {
        $classModel = ClassModel::findOrFail($id);

        // Eager load jadwal dengan mapel dan guru
        $schedules = Schedule::with(['subject', 'teacher'])
            ->where('class_id', $classModel->id)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        $subjects = Subject::orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.schedules.show', compact('classModel', 'schedules', 'subjects', 'days'));
    }

    /**
     * Menampilkan form tambah jadwal.
     */
    public function create(Request $request)
    {
        $class_id = $request->query('class_id');
        $preselectedClass = null;
        if ($class_id) {
            $preselectedClass = ClassModel::find($class_id);
        }

        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        // Ambil data guru
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.schedules.create', compact('classes', 'subjects', 'teachers', 'days', 'preselectedClass'));
    }

    /**
     * Menyimpan jadwal baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id', // Validasi guru
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'class_id.required' => 'Kelas harus dipilih.',
            'subject_id.required' => 'Mata pelajaran harus dipilih.',
            'teacher_id.required' => 'Guru pengampu harus dipilih.',
            'day.required' => 'Hari harus dipilih.',
            'start_time.required' => 'Jam mulai harus diisi.',
            'end_time.required' => 'Jam selesai harus diisi.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        Schedule::create($request->all());

        return redirect()->route('admin.schedules.show', $request->class_id)->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit jadwal.
     */
    public function edit(Schedule $schedule)
    {
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get(); // Ambil data guru
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.schedules.edit', compact('schedule', 'classes', 'subjects', 'teachers', 'days'));
    }

    /**
     * Memperbarui jadwal.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id', // Validasi admin
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'subject_id.required' => 'Mata pelajaran harus dipilih.',
            'teacher_id.required' => 'Guru pengampu harus dipilih.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.show', $schedule->class_id)->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal.
     */
    public function destroy(Schedule $schedule)
    {
        $classId = $schedule->class_id;
        $schedule->delete();
        return redirect()->route('admin.schedules.show', $classId)->with('success', 'Jadwal berhasil dihapus.');
    }
}
