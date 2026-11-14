<?php

namespace Tests\Feature;

use App\Models\Formation;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SkbPracticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_skb_practice_filters_by_formation_and_excludes_skd(): void
    {
        $guru = Formation::factory()->create(['name' => 'Guru']);
        $kesehatan = Formation::factory()->create(['name' => 'Tenaga Kesehatan']);

        $skbGuru = $this->skbQuestion($guru, 'Soal SKB guru?');
        $this->skbQuestion($kesehatan, 'Soal SKB kesehatan?');
        Question::factory()->create(['body' => 'Soal SKD?', 'exam_type' => 'skd']);

        $this->actingAs(User::factory()->create())
            ->get('/practice?mode=skb&formation='.$guru->id)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Practice/Index')
                ->where('filters.mode', 'skb')
                ->where('filters.formation', $guru->id)
                ->has('questions', 1)
                ->where('questions.0.id', $skbGuru->id)
                ->where('questions.0.label', 'Guru')
            );

        $this->actingAs(User::factory()->create())
            ->get('/practice')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.mode', 'skd')
                ->has('questions', 1)
                ->where('questions.0.body', 'Soal SKD?')
            );
    }

    public function test_skb_answer_check_reveals_the_key(): void
    {
        $formation = Formation::factory()->create();
        $question = $this->skbQuestion($formation, 'Soal SKB?');
        $correct = $question->options()->where('is_correct', true)->sole();

        $this->actingAs(User::factory()->create())
            ->postJson("/practice/questions/{$question->id}/check", ['option_id' => $correct->id])
            ->assertOk()
            ->assertJson([
                'is_correct' => true,
                'correct_option_id' => $correct->id,
            ]);
    }

    public function test_admin_can_create_an_skb_question(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $formation = Formation::factory()->create();

        $this->actingAs($admin)
            ->post('/admin/questions', [
                'exam_type' => 'skb',
                'formation_id' => $formation->id,
                'body' => '<p>Soal SKB baru?</p>',
                'explanation' => '<p>Pembahasan.</p>',
                'difficulty' => 2,
                'options' => collect(['A', 'B', 'C', 'D', 'E'])->map(fn (string $label, int $index): array => [
                    'label' => $label,
                    'body' => 'Pilihan '.$label,
                    'is_correct' => $index === 0,
                    'weight' => 0,
                ])->all(),
            ])
            ->assertRedirect(route('admin.questions.index'));

        $this->assertDatabaseHas('questions', [
            'exam_type' => 'skb',
            'formation_id' => $formation->id,
            'subtest_id' => null,
        ]);
    }

    private function skbQuestion(Formation $formation, string $body): Question
    {
        $question = Question::factory()->create([
            'exam_type' => 'skb',
            'formation_id' => $formation->id,
            'subtest_id' => null,
            'topic_id' => null,
            'body' => $body,
        ]);

        foreach (['A', 'B', 'C', 'D', 'E'] as $index => $label) {
            Option::create([
                'question_id' => $question->id,
                'label' => $label,
                'body' => 'Pilihan '.$label,
                'is_correct' => $index === 0,
                'weight' => 0,
            ]);
        }

        return $question;
    }
}
