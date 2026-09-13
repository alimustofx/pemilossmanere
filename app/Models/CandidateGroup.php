<?php

namespace App\Models;

use Database\Factories\CandidateGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateGroup extends Model
{
    /** @use HasFactory<CandidateGroupFactory> */
    use HasFactory;

    protected $fillable = ['election_id', 'nomor_urut', 'nama_kelompok', 'model', 'visi', 'misi'];

    /**
     * @return BelongsTo<Election, $this>
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * @return HasMany<Candidate, $this>
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * @return HasMany<CandidateProgram, $this>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(CandidateProgram::class);
    }

    /**
     * @return HasMany<Vote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
