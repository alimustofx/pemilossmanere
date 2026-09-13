<?php

namespace App\Events;

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteRecorded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Election $election) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('election.'.$this->election->type),
        ];
    }

    public function broadcastAs(): string
    {
        return 'vote.recorded';
    }

    public function broadcastWith(): array
    {
        $results = Vote::where('election_id', $this->election->id)
            ->selectRaw('candidate_group_id, count(*) as total')
            ->groupBy('candidate_group_id')
            ->with('candidateGroup:id,nama_kelompok,nomor_urut')
            ->get();

        return ['results' => $results];
    }
}
