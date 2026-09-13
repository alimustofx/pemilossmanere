<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => Announcement::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('announcements', 'public')
            : null;

        Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('announcements', 'public')
            : $announcement->image;

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
}
