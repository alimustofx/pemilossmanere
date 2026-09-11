<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProgram extends Model
{
    protected $fillable = ['candidate_group_id', 'title', 'description', 'order'];

    public function candidateGroup()
    {
        return $this->belongsTo(CandidateGroup::class);
    }
}
