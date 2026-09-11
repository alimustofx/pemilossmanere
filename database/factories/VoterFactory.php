<?php

namespace Database\Factories;

use App\Models\Voter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voter>
 */
class VoterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('#####'),
            'name' => fake()->name(),
            'class_name' => fake()->randomElement(['X-A', 'X-B', 'XI-A']),
            'tanggal_lahir' => fake()->date(),
            'status' => true,
        ];
    }
}
