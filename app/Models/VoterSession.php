<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoterSession extends Model
{
    protected $fillable = ['voter_id', 'session_token_hash', 'expires_at', 'last_activity_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}
