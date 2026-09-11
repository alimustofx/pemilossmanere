<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    public $timestamps = false;

    protected $fillable = ['election_id', 'candidate_group_id', 'vote_hash', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidateGroup()
    {
        return $this->belongsTo(CandidateGroup::class);
    }
}
