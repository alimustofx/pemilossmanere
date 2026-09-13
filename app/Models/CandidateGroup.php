<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateGroup extends Model
{
    use HasFactory;

    protected $fillable = ['election_id', 'nomor_urut', 'nama_kelompok', 'model', 'visi', 'misi'];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function programs()
    {
        return $this->hasMany(CandidateProgram::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
