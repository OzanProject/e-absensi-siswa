<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SchoolTeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        return view('admin.school_teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.school_teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'is_approved' => true,
        ]);

        return redirect()->route('admin.school-teachers.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = User::findOrFail($id);

        // Ensure strictly viewing a teacher
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        // Get schedules/subjects taught by this teacher
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['class', 'subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return view('admin.school_teachers.show', compact('teacher', 'schedules'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $teacher = User::findOrFail($id);

        if ($teacher->role !== 'guru') {
            abort(404);
        }

        return view('admin.school_teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = User::findOrFail($id);

        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        return redirect()->route('admin.school-teachers.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = User::findOrFail($id);

        if ($teacher->role !== 'guru') {
            abort(404);
        }

        // Check if teacher has schedules
        if (Schedule::where('teacher_id', $teacher->id)->exists()) {
            return redirect()->route('admin.school-teachers.index')
                ->with('error', 'Guru tidak dapat dihapus karena masih memiliki jadwal mengajar.');
        }

        $teacher->delete();

        return redirect()->route('admin.school-teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
