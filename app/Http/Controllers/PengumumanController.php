<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Inertia\Inertia;
use Inertia\Response;

class PengumumanController extends Controller
{
    public function index(): Response
    {
        $announcements = Announcement::where('status', 'published')
            ->latest()
            ->get();

        return Inertia::render('Pengumuman', [
            'announcements' => $announcements,
        ]);
    }
}
