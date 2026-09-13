<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Election;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function index(): Response
    {
        $elections = Election::select('id', 'name', 'type', 'status')->get();

        $latestAnnouncement = Announcement::where('status', 'published')
            ->latest()
            ->first();

        return Inertia::render('Landing', [
            'elections' => $elections,
            'latestAnnouncement' => $latestAnnouncement,
        ]);
    }
}
