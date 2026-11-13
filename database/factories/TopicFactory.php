<?php

namespace Database\Factories;

use App\Models\Subtest;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Topic>
 */
class TopicFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subtest_id' => Subtest::factory(),
            'name' => fake()->unique()->words(2, true),
        ];
    }
}
