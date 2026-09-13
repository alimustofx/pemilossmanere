<?php

namespace App\Http\Controllers;

use App\Events\VoteRecorded;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VoteController extends Controller
{
    public function osis(Request $request): Response|RedirectResponse
    {
        $voter = $request->attributes->get('voter');

        if ($voter->osis_voted_at) {
            return redirect()->route('vote.mpk');
        }

        $election = Election::where('type', 'osis')
            ->where('status', 'open')
            ->firstOrFail();

        $election->load([
            'candidateGroups.candidates',
            'candidateGroups.programs',
        ]);

        return Inertia::render('Vote/Osis', [
            'election' => $election,
        ]);
    }

    public function storeOsis(Request $request): RedirectResponse
    {
        $voter = $request->attributes->get('voter');

        if ($voter->osis_voted_at) {
            return redirect()->route('vote.mpk');
        }

        $validated = $request->validate([
            'candidate_group_id' => 'required|exists:candidate_groups,id',
        ]);

        $election = Election::where('type', 'osis')
            ->where('status', 'open')
            ->firstOrFail();

        Vote::create([
            'election_id' => $election->id,
            'candidate_group_id' => $validated['candidate_group_id'],
            'vote_hash' => Str::uuid(),
            'created_at' => now(),
        ]);

        $voter->update([
            'osis_voted_at' => now(),
        ]);

        // Beritahu frontend/admin bahwa ada vote baru
        VoteRecorded::dispatch($election);

        return redirect()->route('vote.mpk');
    }

    public function mpk(Request $request): Response|RedirectResponse
    {
        $voter = $request->attributes->get('voter');

        if (! $voter->osis_voted_at) {
            return redirect()->route('vote.osis');
        }

        if ($voter->mpk_voted_at) {
            return redirect()->route('vote.done');
        }

        $election = Election::where('type', 'mpk')
            ->where('status', 'open')
            ->firstOrFail();

        $election->load([
            'candidateGroups.candidates',
            'candidateGroups.programs',
        ]);

        return Inertia::render('Vote/Mpk', [
            'election' => $election,
        ]);
    }

    public function storeMpk(Request $request): RedirectResponse
    {
        $voter = $request->attributes->get('voter');

        if ($voter->mpk_voted_at) {
            return redirect()->route('vote.done');
        }

        $validated = $request->validate([
            'candidate_group_id' => 'required|exists:candidate_groups,id',
        ]);

        $election = Election::where('type', 'mpk')
            ->where('status', 'open')
            ->firstOrFail();

        Vote::create([
            'election_id' => $election->id,
            'candidate_group_id' => $validated['candidate_group_id'],
            'vote_hash' => Str::uuid(),
            'created_at' => now(),
        ]);

        $voter->update([
            'mpk_voted_at' => now(),
        ]);

        // Beritahu frontend/admin bahwa ada vote baru
        VoteRecorded::dispatch($election);

        return redirect()->route('vote.done');
    }

    public function done(): Response
    {
        return Inertia::render('Vote/Done');
    }
}
