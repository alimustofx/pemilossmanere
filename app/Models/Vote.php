<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Election|null $election
 * @property-read CandidateGroup|null $candidateGroup
 */
class Vote extends Model
{
    public $timestamps = false;

    protected $fillable = ['election_id', 'candidate_group_id', 'vote_hash', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Election, $this>
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * @return BelongsTo<CandidateGroup, $this>
     */
    public function candidateGroup(): BelongsTo
    {
        return $this->belongsTo(CandidateGroup::class);
    }
}
