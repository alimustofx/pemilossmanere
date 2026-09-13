<?php

namespace App\Http\Middleware;

use App\Models\VoterSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVoterSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->cookie('voter_session');

        if (! is_string($rawToken) || $rawToken === '') {
            return redirect()->route('vote.access');
        }

        $hash = hash('sha256', $rawToken);

        $session = VoterSession::with('voter')
            ->where('session_token_hash', $hash)
            ->where('expires_at', '>', now())
            ->first();

        if (! $session) {
            return redirect()->route('vote.access');
        }

        $session->update(['last_activity_at' => now()]);

        // Simpan voter ke request agar bisa dipakai controller lain
        $request->attributes->set('voter', $session->voter);

        return $next($request);
    }
}
