<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Subtest;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_lists_only_own_submitted_attempts(): void
    {
        $user = User::factory()->create();
        $tryout = Tryout::create(['title' => 'Paket A', 'duration_minutes' => 100]);
        Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 65]);

        Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subHour(),
            'ends_at' => now(),
            'submitted_at' => now(),
            'total_score' => 300,
            'is_passed' => true,
        ]);

        Attempt::create([
            'user_id' => User::factory()->create()->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subHour(),
            'ends_at' => now(),
            'submitted_at' => now(),
            'total_score' => 999,
        ]);

        Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($user)->get('/history')->assertOk();
        $props = $response->viewData('page')['props'];

        $this->assertCount(1, $props['attempts']['data']);
        $this->assertSame(300, $props['attempts']['data'][0]['score']);
        $this->assertSame([300], $props['trend']);
    }
}
