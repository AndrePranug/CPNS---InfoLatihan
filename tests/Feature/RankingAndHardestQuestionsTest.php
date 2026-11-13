<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RankingAndHardestQuestionsTest extends TestCase
{
    use RefreshDatabase;

    private function submitted(User $user, Tryout $tryout, int $score): Attempt
    {
        return Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now()->subHour(),
            'ends_at' => now(),
            'submitted_at' => now(),
            'total_score' => $score,
        ]);
    }

    public function test_leaderboard_lists_best_score_with_participant_name(): void
    {
        $me = User::factory()->create(['name' => 'Saya']);
        $top = User::factory()->create(['name' => 'Juara']);
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        $this->submitted($top, $tryout, 400);
        $this->submitted($top, $tryout, 500);
        $this->submitted($me, $tryout, 300);

        $rows = app(ProgressService::class)->leaderboard($me);

        $this->assertSame('Juara', $rows[0]['name']);
        $this->assertSame(500, $rows[0]['score']);
        $this->assertFalse($rows[0]['is_me']);
        $this->assertSame('Saya', $rows[1]['name']);
        $this->assertTrue($rows[1]['is_me']);
    }

    public function test_tryout_ranking_uses_best_score_per_participant(): void
    {
        $me = User::factory()->create(['name' => 'Saya']);
        $best = User::factory()->create(['name' => 'Juara']);
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        $this->submitted($best, $tryout, 500);
        $this->submitted($best, $tryout, 400);
        $this->submitted($me, $tryout, 450);

        $ranking = app(ProgressService::class)->tryoutRanking($tryout, $me);

        $this->assertSame(1, $ranking['rows'][0]['position']);
        $this->assertSame('Juara', $ranking['rows'][0]['name']);
        $this->assertSame(500, $ranking['rows'][0]['score']);
        $this->assertSame(2, $ranking['position']);
        $this->assertSame(2, $ranking['total']);
    }

    public function test_ranking_excludes_unsubmitted_attempts(): void
    {
        $user = User::factory()->create();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addHour(),
        ]);

        $this->assertSame(0, app(ProgressService::class)->tryoutRanking($tryout, $user)['total']);
    }

    public function test_result_page_exposes_ranking(): void
    {
        $me = User::factory()->create();
        $rival = User::factory()->create(['name' => 'Rival']);
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);
        $attempt = $this->submitted($me, $tryout, 300);
        $this->submitted($rival, $tryout, 400);

        $this->actingAs($me)->get("/attempts/{$attempt->id}/result")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Results/Show')
                ->where('ranking.position', 2)
                ->where('ranking.rows.0.name', 'Rival')
            );
    }

    public function test_admin_dashboard_lists_lowest_correct_rate_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        $subtest = Subtest::query()->firstOrCreate(
            ['code' => 'TWK'],
            ['name' => 'TWK', 'question_count' => 30, 'passing_grade' => 65],
        );
        $topic = Topic::query()->firstOrCreate(['subtest_id' => $subtest->id, 'name' => 'Pancasila']);

        $easy = Question::factory()->create(['subtest_id' => $subtest->id, 'topic_id' => $topic->id, 'body' => 'Soal mudah?']);
        $hard = Question::factory()->create(['subtest_id' => $subtest->id, 'topic_id' => $topic->id, 'body' => 'Soal sulit?']);

        $attempt = $this->submitted($user, $tryout, 0);

        foreach ([[$easy, true], [$hard, false]] as [$question, $isCorrect]) {
            $option = Option::create([
                'question_id' => $question->id,
                'label' => 'A',
                'body' => 'Pilihan',
                'is_correct' => $isCorrect,
                'weight' => $isCorrect ? 5 : 0,
            ]);

            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $option->id,
            ]);
        }

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->has('hardestQuestions', 2)
                ->where('hardestQuestions.0.body', 'Soal sulit?')
                ->where('hardestQuestions.0.correct_rate', 0)
                ->where('hardestQuestions.1.correct_rate', 100)
            );
    }
}
