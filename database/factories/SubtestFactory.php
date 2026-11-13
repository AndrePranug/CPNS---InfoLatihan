<?php

namespace Database\Factories;

use App\Enums\SubtestCode;
use App\Models\Subtest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subtest>
 */
class SubtestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->randomElement(SubtestCode::cases()),
            'name' => fake()->words(3, true),
            'question_count' => 30,
            'passing_grade' => 65,
        ];
    }
}
