<?php

namespace Database\Factories;

use App\Models\CandidateGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateGroup>
 */
class CandidateGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_urut' => fake()->numberBetween(1, 5),
            'nama_kelompok' => fake()->name() . ' & ' . fake()->name(),
            'model' => 'pair',
        ];
    }
}
