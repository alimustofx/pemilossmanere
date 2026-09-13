<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use App\Models\VoterSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VoteAccessController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Vote/Access');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'nis' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        // Batasi percobaan berdasarkan NIS,
        // bukan berdasarkan IP address.
        $key = 'vote-access:'.$validated['nis'];

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'nis' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $voter = Voter::where('nis', $validated['nis'])
            ->where('class_name', $validated['class_name'])
            ->whereDate('tanggal_lahir', $validated['tanggal_lahir'])
            ->where('status', true)
            ->first();

        if (! $voter) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'nis' => 'Data tidak ditemukan atau tidak aktif. Periksa kembali kelas, NIS, dan tanggal lahir.',
            ]);
        }

        // Login berhasil, reset counter percobaan.
        RateLimiter::clear($key);

        if ($voter->osis_voted_at && $voter->mpk_voted_at) {
            return back()->withErrors([
                'nis' => 'Anda sudah menyelesaikan seluruh pemilihan.',
            ]);
        }

        // Buat token session mentah, simpan HASH-nya saja di DB
        $rawToken = Str::random(64);

        VoterSession::create([
            'voter_id' => $voter->id,
            'session_token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addHours(2),
            'last_activity_at' => now(),
        ]);

        $cookie = cookie(
            'voter_session',
            $rawToken,
            120,
            null,
            null,
            false,
            true
        );

        $nextRoute = $voter->osis_voted_at ? 'vote.mpk' : 'vote.osis';

        return redirect()->route($nextRoute)->withCookie($cookie);
    }
}
