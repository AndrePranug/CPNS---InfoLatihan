<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attempt>
 */
class AttemptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $started = now()->subMinutes(10);

        return [
            'user_id' => User::factory(),
            'tryout_id' => Tryout::factory(),
            'started_at' => $started,
            'ends_at' => $started->copy()->addMinutes(100),
        ];
    }
}
