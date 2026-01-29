<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::with('class')->latest()->paginate(10);
        $classes = ClassModel::all(); // Untuk modal create/edit
        return view('admin.announcements.index', compact('announcements', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::all();
        return view('admin.announcements.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:all,class',
            'target_id' => 'nullable|required_if:target_type,class|exists:classes,id',
        ]);

        // Sanitasi input content untuk mencegah XSS
        $data = $request->all();
        $allowedTags = '<p><br><b><i><u><ul><ol><li><strong><em>';
        $data['content'] = strip_tags($request->content, $allowedTags);

        Announcement::create($data);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
         return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $classes = ClassModel::all();
        return view('admin.announcements.edit', compact('announcement', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:all,class',
            'target_id' => 'nullable|required_if:target_type,class|exists:classes,id',
            'is_active' => 'boolean',
        ]);

        // Pastikan target_id null jika target_type = all
        $data = $request->all();
        
        // Sanitasi input content untuk mencegah XSS
        $allowedTags = '<p><br><b><i><u><ul><ol><li><strong><em>';
        if (isset($data['content'])) {
            $data['content'] = strip_tags($data['content'], $allowedTags);
        }

        if ($data['target_type'] == 'all') {
            $data['target_id'] = null;
        }

        $announcement->update($data);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
