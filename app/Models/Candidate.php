<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = ['candidate_group_id', 'name', 'role', 'photo'];

    /**
     * @return BelongsTo<CandidateGroup, $this>
     */
    public function candidateGroup(): BelongsTo
    {
        return $this->belongsTo(CandidateGroup::class);
    }
}
