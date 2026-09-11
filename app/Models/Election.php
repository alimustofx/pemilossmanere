<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type', 'status', 'starts_at', 'ends_at'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function candidateGroups()
    {
        return $this->hasMany(CandidateGroup::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
