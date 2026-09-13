<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HasilController extends Controller
{
    public function show(Request $request, string $type)
    {
        $election = Election::where('type', $type)->firstOrFail();

        $results = Vote::where('election_id', $election->id)
            ->selectRaw('candidate_group_id, count(*) as total')
            ->groupBy('candidate_group_id')
            ->with('candidateGroup:id,nama_kelompok,nomor_urut')
            ->get();

        return Inertia::render('Hasil', [
            'election' => $election,
            'initialResults' => $results,
        ]);
    }
}
