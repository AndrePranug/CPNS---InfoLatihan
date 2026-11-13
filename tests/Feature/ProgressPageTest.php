<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProgressPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_page_reports_summary_and_trend(): void
    {
        $user = User::factory()->create();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        foreach ([[300, 5], [450, 1]] as [$score, $daysAgo]) {
            Attempt::create([
                'user_id' => $user->id,
                'tryout_id' => $tryout->id,
                'started_at' => now()->subDays($daysAgo)->subMinutes(30),
                'ends_at' => now()->subDays($daysAgo),
                'submitted_at' => now()->subDays($daysAgo)->startOfDay()->addHours(9),
                'score_twk' => 100,
                'score_tiu' => 100,
                'score_tkp' => $score - 200,
                'total_score' => $score,
            ]);
        }

        $this->actingAs($user)->get('/progress')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Progress/Index')
                ->where('summary.attempts', 2)
                ->where('summary.best', 450)
                ->where('summary.average', 375)
                ->where('summary.improvement', 50)
                ->has('trend', 2)
            );
    }

    public function test_progress_page_is_empty_safe(): void
    {
        $this->actingAs(User::factory()->create())->get('/progress')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('summary.attempts', 0)
                ->where('summary.improvement', 0)
                ->has('trend', 0)
            );
    }
}
