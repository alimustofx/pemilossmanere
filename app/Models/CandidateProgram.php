<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProgram extends Model
{
    protected $fillable = ['candidate_group_id', 'title', 'description', 'order'];

    /**
     * @return BelongsTo<CandidateGroup, $this>
     */
    public function candidateGroup(): BelongsTo
    {
        return $this->belongsTo(CandidateGroup::class);
    }
}
