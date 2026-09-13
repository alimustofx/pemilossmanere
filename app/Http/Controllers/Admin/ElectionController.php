<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ElectionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Elections/Index', [
            'elections' => Election::orderBy('type')->get(),
        ]);
    }

    public function open(Election $election): RedirectResponse
    {
        $election->update([
            'status' => 'open',
            'starts_at' => $election->starts_at ?? now(),
        ]);

        AuditLogger::log("Membuka voting: {$election->name}");

        return back()->with('success', "Voting {$election->name} dibuka.");
    }

    public function close(Election $election): RedirectResponse
    {
        $election->update([
            'status' => 'closed',
            'ends_at' => now(),
        ]);

        AuditLogger::log("Menutup voting: {$election->name}");

        return back()->with('success', "Voting {$election->name} ditutup.");
    }

    public function publish(Election $election): RedirectResponse
    {
        // Hanya bisa publish kalau election ini closed
        if ($election->status !== 'closed') {
            return back()->withErrors([
                'status' => 'Election harus ditutup dulu sebelum hasil dipublikasikan.',
            ]);
        }

        $election->update([
            'status' => 'result_published',
        ]);

        AuditLogger::log("Publikasi hasil: {$election->name}");

        return back()->with('success', "Hasil {$election->name} dipublikasikan.");
    }
}
