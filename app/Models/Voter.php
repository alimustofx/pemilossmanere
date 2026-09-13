<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use HasFactory;

    protected $fillable = ['nis', 'name', 'class_name', 'tanggal_lahir', 'status', 'osis_voted_at', 'mpk_voted_at'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status' => 'boolean',
        'osis_voted_at' => 'datetime',
        'mpk_voted_at' => 'datetime',
    ];

    public function sessions()
    {
        return $this->hasMany(VoterSession::class);
    }
}
