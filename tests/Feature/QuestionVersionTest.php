<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Tryout;
use App\Models\User;
use App\Services\QuestionVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuestionVersionTest extends TestCase
{
    use RefreshDatabase;

    private function questionWithOptions(): Question
    {
        $subtest = Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 65]);
        $question = Question::create(['subtest_id' => $subtest->id, 'body' => 'Soal v1?']);

        Option::create(['question_id' => $question->id, 'label' => 'A', 'body' => 'a', 'is_correct' => true, 'weight' => 5]);
        Option::create(['question_id' => $question->id, 'label' => 'B', 'body' => 'b', 'is_correct' => false, 'weight' => 0]);

        return $question;
    }

    public function test_revising_creates_new_version_and_soft_deletes_old_one(): void
    {
        $question = $this->questionWithOptions();

        $new = app(QuestionVersionService::class)->revise($question);

        $this->assertSame(2, $new->fresh()->version);
        $this->assertCount(2, $new->options);
        $this->assertSoftDeleted('questions', ['id' => $question->id]);
        $this->assertDatabaseHas('options', ['question_id' => $new->id, 'label' => 'A', 'is_correct' => true]);
    }

    public function test_revision_blocked_while_attempt_still_running(): void
    {
        $question = $this->questionWithOptions();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);
        $user = User::factory()->create();
        $attempt = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addMinutes(100),
        ]);
        $attempt->answers()->create(['question_id' => $question->id, 'answered_at' => now()]);

        $this->expectException(ValidationException::class);

        app(QuestionVersionService::class)->revise($question);
    }

    public function test_admin_sees_edit_form_with_existing_options(): void
    {
        $question = $this->questionWithOptions();
        $admin = User::factory()->create(['role' => Role::Admin]);

        $page = $this->actingAs($admin)->get("/admin/questions/{$question->id}/edit")->assertOk()->viewData('page')['props'];

        $this->assertSame('Soal v1?', $page['question']['body']);
        $this->assertCount(2, $page['question']['options']);
    }
}
