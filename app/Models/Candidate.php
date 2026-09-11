<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = ['candidate_group_id', 'name', 'role', 'photo'];

    public function candidateGroup()
    {
        return $this->belongsTo(CandidateGroup::class);
    }
}
