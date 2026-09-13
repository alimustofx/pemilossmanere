<?php

namespace Tests;

use App\Models\Voter;
use App\Models\VoterSession;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    protected function actingAsVoter(Voter $voter): static
    {
        $rawToken = Str::random(64);

        VoterSession::create([
            'voter_id' => $voter->id,
            'session_token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addHours(2),
            'last_activity_at' => now(),
        ]);

        return $this->withCookie('voter_session', $rawToken);
    }
}
