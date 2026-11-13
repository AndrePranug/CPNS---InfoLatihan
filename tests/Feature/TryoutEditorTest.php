<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ExamOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TryoutEditorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function subtest(): Subtest
    {
        return Subtest::query()->firstOrCreate(
            ['code' => 'TWK'],
            ['name' => 'Tes Wawasan Kebangsaan', 'question_count' => 30, 'passing_grade' => 65],
        );
    }

    private function topic(Subtest $subtest): Topic
    {
        return Topic::query()->firstOrCreate(
            ['subtest_id' => $subtest->id, 'name' => 'Nasionalisme'],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function questions(array $attributes = [], int $count = 1): Collection
    {
        $subtest = $this->subtest();

        return Question::factory()->count($count)->create([
            'subtest_id' => $subtest->id,
            'topic_id' => $this->topic($subtest)->id,
            ...$attributes,
        ]);
    }

    public function test_admin_sees_bank_and_selected_questions(): void
    {
        $subtest = $this->subtest();
        $question = $this->questions()->first();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);
        $tryout->questions()->attach($question->id, ['position' => 1]);

        $this->actingAs($this->admin())
            ->get("/admin/tryouts/{$tryout->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Tryouts/Edit')
                ->where('selected', [$question->id])
                ->has('bank', 1)
                ->where('bank.0.id', $question->id)
            );
    }

    public function test_sync_questions_stores_positions_in_given_order(): void
    {
        $questions = $this->questions(count: 3);
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        $this->actingAs($this->admin())
            ->post("/admin/tryouts/{$tryout->id}/questions", [
                'question_ids' => [$questions[2]->id, $questions[0]->id, $questions[1]->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $ordered = $tryout->questions()->orderBy('tryout_question.position')->pluck('questions.id')->all();

        $this->assertSame([$questions[2]->id, $questions[0]->id, $questions[1]->id], $ordered);
    }

    public function test_empty_sync_requires_confirmation(): void
    {
        $question = $this->questions()->first();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);
        $tryout->questions()->attach($question->id, ['position' => 1]);

        $this->actingAs($this->admin())
            ->post("/admin/tryouts/{$tryout->id}/questions", ['question_ids' => []])
            ->assertSessionHas('error');

        $this->assertSame(1, $tryout->questions()->count());

        $this->actingAs($this->admin())
            ->post("/admin/tryouts/{$tryout->id}/questions", ['question_ids' => [], 'confirm_empty' => true])
            ->assertSessionHas('success');

        $this->assertSame(0, $tryout->questions()->count());
    }

    public function test_shuffle_is_stable_per_attempt_and_differs_between_attempts(): void
    {
        $service = app(ExamOrderService::class);
        $user = User::factory()->create();
        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);

        $first = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addHour(),
        ]);
        $second = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addHour(),
        ]);

        $ids = range(1, 20);

        $this->assertSame($service->shuffle($ids, $first, 'question'), $service->shuffle($ids, $first, 'question'));
        $this->assertNotSame($service->shuffle($ids, $first, 'question'), $service->shuffle($ids, $second, 'question'));
        $this->assertEqualsCanonicalizing($ids, $service->shuffle($ids, $first, 'question'));
    }
}
