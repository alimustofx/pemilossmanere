<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Inertia\Inertia;

class PengumumanController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('status', 'published')
            ->latest()
            ->get();

        return Inertia::render('Pengumuman', [
            'announcements' => $announcements,
        ]);
    }
}
