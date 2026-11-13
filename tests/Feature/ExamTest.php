<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tryout, 2: Question}
     */
    private function scenario(): array
    {
        $user = User::factory()->create();
        $twk = Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 5]);
        Subtest::create(['code' => 'TIU', 'name' => 'TIU', 'question_count' => 35, 'passing_grade' => 0]);
        Subtest::create(['code' => 'TKP', 'name' => 'TKP', 'question_count' => 45, 'passing_grade' => 0]);
        $question = Question::create(['subtest_id' => $twk->id, 'body' => 'Soal?', 'difficulty' => 1]);

        foreach (['A', 'B'] as $i => $label) {
            Option::create([
                'question_id' => $question->id,
                'label' => $label,
                'body' => "Pilihan {$label}",
                'is_correct' => $i === 0,
                'weight' => $i === 0 ? 5 : 0,
            ]);
        }

        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100, 'is_published' => true]);
        $tryout->questions()->attach($question->id, ['position' => 1]);

        return [$user, $tryout, $question];
    }

    public function test_starting_creates_attempt_with_server_ends_at_and_is_idempotent(): void
    {
        [$user, $tryout] = $this->scenario();

        $this->actingAs($user)->post("/tryouts/{$tryout->id}/start")->assertRedirect();

        $first = Attempt::sole();
        $this->assertSame(6000, (int) $first->started_at->diffInSeconds($first->ends_at));

        $this->actingAs($user)->post("/tryouts/{$tryout->id}/start");
        $this->assertSame(1, Attempt::count());
    }

    public function test_exam_payload_never_exposes_answer_key(): void
    {
        [$user, $tryout] = $this->scenario();
        $this->actingAs($user)->post("/tryouts/{$tryout->id}/start");
        $attempt = Attempt::sole();

        $response = $this->actingAs($user)->get("/attempts/{$attempt->id}");
        $response->assertOk();

        $payload = $response->viewData('page')['props'];
        $encoded = json_encode($payload['questions']);

        $this->assertStringNotContainsString('is_correct', $encoded);
        $this->assertStringNotContainsString('weight', $encoded);
    }

    public function test_answers_autosave_and_are_scored_per_subtest(): void
    {
        [$user, $tryout, $question] = $this->scenario();
        $this->actingAs($user)->post("/tryouts/{$tryout->id}/start");
        $attempt = Attempt::sole();
        $correct = $question->options()->where('label', 'A')->sole();

        $this->actingAs($user)->postJson("/attempts/{$attempt->id}/answer", [
            'question_id' => $question->id,
            'option_id' => $correct->id,
            'is_flagged' => true,
        ])->assertOk();

        $this->assertDatabaseHas('attempt_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $correct->id,
            'is_flagged' => true,
        ]);

        $this->actingAs($user)->post("/attempts/{$attempt->id}/submit")
            ->assertRedirect("/attempts/{$attempt->id}/result");

        $attempt->refresh();
        $this->assertSame(5, $attempt->score_twk);
        $this->assertSame(5, $attempt->total_score);
        $this->assertTrue($attempt->is_passed);
        $this->assertNotNull($attempt->submitted_at);
    }

    public function test_answer_after_expiry_is_rejected(): void
    {
        [$user, $tryout, $question] = $this->scenario();
        $this->actingAs($user)->post("/tryouts/{$tryout->id}/start");
        $attempt = Attempt::sole();
        $attempt->update(['ends_at' => now()->subMinute()]);

        $this->actingAs($user)->postJson("/attempts/{$attempt->id}/answer", [
            'question_id' => $question->id,
            'option_id' => null,
            'is_flagged' => false,
        ])->assertStatus(422);

        $attempt->refresh();
        $this->assertNotNull($attempt->submitted_at, 'expired attempt is auto-submitted');
    }

    public function test_user_cannot_open_other_users_attempt(): void
    {
        [$owner, $tryout] = $this->scenario();
        $this->actingAs($owner)->post("/tryouts/{$tryout->id}/start");
        $attempt = Attempt::sole();

        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get("/attempts/{$attempt->id}")->assertForbidden();
    }

    public function test_exam_service_remaining_seconds_never_negative(): void
    {
        [$user, $tryout] = $this->scenario();
        $this->actingAs($user)->post("/tryouts/{$tryout->id}/start");
        $attempt = Attempt::sole();

        $this->assertGreaterThan(0, app(ExamService::class)->remainingSeconds($attempt));

        $attempt->update(['ends_at' => now()->subHour()]);
        $this->assertSame(0, app(ExamService::class)->remainingSeconds($attempt));
    }
}
