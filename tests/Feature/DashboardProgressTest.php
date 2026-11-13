<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\DailyActivity;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_streak_counts_consecutive_days_hitting_target(): void
    {
        $user = User::factory()->create();
        $target = ProgressService::DAILY_TARGET_MINUTES;

        foreach ([0, 1, 2] as $daysAgo) {
            DailyActivity::create([
                'user_id' => $user->id,
                'activity_date' => now()->subDays($daysAgo)->toDateString(),
                'study_minutes' => $target,
            ]);
        }

        DailyActivity::create([
            'user_id' => $user->id,
            'activity_date' => now()->subDays(5)->toDateString(),
            'study_minutes' => $target,
        ]);

        $this->assertSame(3, app(ProgressService::class)->streak($user));
    }

    public function test_streak_breaks_when_yesterday_and_today_missed(): void
    {
        $user = User::factory()->create();

        DailyActivity::create([
            'user_id' => $user->id,
            'activity_date' => now()->subDays(3)->toDateString(),
            'study_minutes' => 60,
        ]);

        $this->assertSame(0, app(ProgressService::class)->streak($user));
    }

    public function test_dashboard_exposes_goal_resume_and_leaderboard(): void
    {
        $user = User::factory()->create(['name' => 'Peserta Satu']);
        $rival = User::factory()->create(['name' => 'Peserta Dua']);
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100, 'is_published' => true]);

        Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subMinutes(30),
            'ends_at' => now()->addMinutes(70),
        ]);

        Attempt::create([
            'user_id' => $rival->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subHours(2),
            'ends_at' => now()->subHour(),
            'submitted_at' => now()->subHour(),
            'total_score' => 400,
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('goal.target', ProgressService::DAILY_TARGET_MINUTES)
                ->has('goal.days', 7)
                ->where('resume.title', 'Paket')
                ->where('leaderboard.0.name', 'Peserta Dua')
                ->where('leaderboard.0.score', 400)
            );
    }

    public function test_submitting_attempt_records_daily_activity_once(): void
    {
        $user = User::factory()->create();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);
        $attempt = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subMinutes(25),
            'ends_at' => now()->addMinutes(75),
        ]);

        app(ProgressService::class)->recordAttempt($attempt);
        app(ProgressService::class)->recordAttempt($attempt->fresh());

        $activity = DailyActivity::query()->where('user_id', $user->id)->sole();

        $this->assertSame(1, DailyActivity::count());
        $this->assertSame(25, $activity->study_minutes);
    }
}
