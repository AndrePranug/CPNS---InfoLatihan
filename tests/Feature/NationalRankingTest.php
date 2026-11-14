<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NationalRankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_national_ranking_uses_best_score_and_marks_current_user(): void
    {
        $me = User::factory()->create(['name' => 'Saya']);
        $top = User::factory()->create(['name' => 'Top']);
        $tryout = Tryout::factory()->create();

        $this->attempt($me, $tryout, 450);
        $this->attempt($me, $tryout, 200);
        $this->attempt($top, $tryout, 500);

        $ranking = app(ProgressService::class)->nationalRanking($me);

        $this->assertSame(2, $ranking['total']);
        $this->assertSame(2, $ranking['position']);
        $this->assertSame(450, $ranking['score']);
        $this->assertSame('Top', $ranking['rows'][0]['name']);
        $this->assertSame(500, $ranking['rows'][0]['score']);
        $this->assertTrue($ranking['rows'][1]['is_me']);
    }

    public function test_ranking_page_renders_and_filters_by_period(): void
    {
        $me = User::factory()->create(['name' => 'Saya']);
        $tryout = Tryout::factory()->create();

        $this->attempt($me, $tryout, 400);
        $this->attempt($me, $tryout, 100, now()->subMonth());

        $this->actingAs($me)
            ->get('/rankings?period=week')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Rankings/Index')
                ->where('ranking.period', 'week')
                ->where('ranking.total', 1)
                ->where('ranking.rows.0.score', 400)
            );
    }

    private function attempt(User $user, Tryout $tryout, int $score, ?\DateTimeInterface $submittedAt = null): void
    {
        Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subHour(),
            'ends_at' => now(),
            'submitted_at' => $submittedAt ?? now(),
            'total_score' => $score,
        ]);
    }
}
