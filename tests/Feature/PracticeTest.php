<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    private Subtest $subtest;

    private Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subtest = Subtest::query()->firstOrCreate(
            ['code' => 'TWK'],
            ['name' => 'Tes Wawasan Kebangsaan', 'question_count' => 30, 'passing_grade' => 65],
        );

        $this->topic = Topic::query()->firstOrCreate(
            ['subtest_id' => $this->subtest->id, 'name' => 'Pancasila'],
        );
    }

    private function question(bool $withKey = true): Question
    {
        $question = Question::factory()->create([
            'subtest_id' => $this->subtest->id,
            'topic_id' => $this->topic->id,
            'explanation' => 'Karena sila pertama.',
        ]);

        foreach (['A', 'B', 'C', 'D', 'E'] as $i => $label) {
            Option::create([
                'question_id' => $question->id,
                'label' => $label,
                'body' => "Pilihan {$label}",
                'is_correct' => $withKey && $i === 1,
                'weight' => $withKey && $i === 1 ? 5 : 0,
            ]);
        }

        return $question;
    }

    public function test_practice_page_lists_questions_without_answer_keys(): void
    {
        $question = $this->question();

        $this->actingAs(User::factory()->create())->get('/practice')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Practice/Index')
                ->has('questions', 1)
                ->where('questions.0.id', $question->id)
                ->missing('questions.0.explanation')
                ->has('questions.0.options.0', fn (Assert $option) => $option
                    ->has('id')
                    ->has('body')
                    ->missing('is_correct')
                    ->missing('weight')
                )
                ->has('subtests')
                ->has('topics')
            );
    }

    public function test_practice_filters_by_subtest(): void
    {
        $this->question();
        $other = Subtest::factory()->create(['code' => 'TIU']);
        $otherTopic = Topic::factory()->create(['subtest_id' => $other->id, 'name' => 'Silogisme']);
        Question::factory()->create(['subtest_id' => $other->id, 'topic_id' => $otherTopic->id]);

        $this->actingAs(User::factory()->create())->get('/practice?subtest=TWK')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('questions', 1));
    }

    public function test_check_reveals_key_for_correct_answer(): void
    {
        $question = $this->question();
        $correct = $question->options()->where('is_correct', true)->sole();

        $this->actingAs(User::factory()->create())
            ->postJson("/practice/questions/{$question->id}/check", ['option_id' => $correct->id])
            ->assertOk()
            ->assertJson([
                'is_correct' => true,
                'correct_option_id' => $correct->id,
                'explanation' => 'Karena sila pertama.',
            ]);
    }

    public function test_check_flags_wrong_answer(): void
    {
        $question = $this->question();
        $wrong = $question->options()->where('is_correct', false)->first();

        $this->actingAs(User::factory()->create())
            ->postJson("/practice/questions/{$question->id}/check", ['option_id' => $wrong->id])
            ->assertOk()
            ->assertJson(['is_correct' => false]);
    }

    public function test_check_rejects_option_from_another_question(): void
    {
        $question = $this->question();
        $foreign = $this->question()->options()->first();

        $this->actingAs(User::factory()->create())
            ->postJson("/practice/questions/{$question->id}/check", ['option_id' => $foreign->id])
            ->assertNotFound();
    }

    public function test_practice_requires_authentication(): void
    {
        $this->get('/practice')->assertRedirect('/login');
    }
}
