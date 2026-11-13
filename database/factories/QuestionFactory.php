<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subtest_id' => Subtest::factory(),
            'topic_id' => Topic::factory(),
            'body' => fake()->sentence().'?',
            'explanation' => fake()->sentence(),
            'difficulty' => fake()->numberBetween(1, 3),
            'version' => 1,
        ];
    }
}
