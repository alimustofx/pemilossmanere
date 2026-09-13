<?php

namespace App\Models;

use Database\Factories\VoterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voter extends Model
{
    /** @use HasFactory<VoterFactory> */
    use HasFactory;

    protected $fillable = ['nis', 'name', 'class_name', 'tanggal_lahir', 'status', 'osis_voted_at', 'mpk_voted_at'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status' => 'boolean',
        'osis_voted_at' => 'datetime',
        'mpk_voted_at' => 'datetime',
    ];

    /**
     * @return HasMany<VoterSession, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(VoterSession::class);
    }
}
