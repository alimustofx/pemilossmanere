<?php

use App\Models\Voter;
use App\Models\Election;
use App\Models\CandidateGroup;
use App\Models\Vote;

test('voter tidak bisa vote dua kali di pemilihan yang sama', function () {
    $voter = Voter::factory()->create(['osis_voted_at' => now()]);
    $election = Election::factory()->create(['type' => 'osis', 'status' => 'open']);
    $group = CandidateGroup::factory()->create(['election_id' => $election->id]);

    $this->withSession([])->actingAsVoter($voter); // helper, lihat catatan di bawah

    $response = $this->post('/vote/osis', ['candidate_group_id' => $group->id]);

    // Harus redirect ke MPK, bukan mencatat vote baru
    $response->assertRedirect('/vote/mpk');
    expect(Vote::where('election_id', $election->id)->count())->toBe(0);
});

test('vote tersimpan dan tidak menyimpan voter_id (kerahasiaan)', function () {
    $election = Election::factory()->create(['type' => 'osis', 'status' => 'open']);
    $group = CandidateGroup::factory()->create(['election_id' => $election->id]);

    Vote::create([
        'election_id' => $election->id,
        'candidate_group_id' => $group->id,
        'vote_hash' => \Illuminate\Support\Str::uuid(),
        'created_at' => now(),
    ]);

    expect(Vote::first()->getAttributes())->not->toHaveKey('voter_id');
});