<?php

namespace Database\Factories;

use App\Models\Tryout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tryout>
 */
class TryoutFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'duration_minutes' => 100,
            'price' => 0,
            'is_published' => true,
        ];
    }
}
