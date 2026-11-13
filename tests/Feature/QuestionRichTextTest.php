<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Subtest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionRichTextTest extends TestCase
{
    use RefreshDatabase;

    private function subtest(): Subtest
    {
        return Subtest::create(['code' => 'TIU', 'name' => 'TIU', 'question_count' => 35, 'passing_grade' => 80]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Subtest $subtest, string $body, ?string $explanation = null): array
    {
        return [
            'subtest_id' => $subtest->id,
            'body' => $body,
            'explanation' => $explanation,
            'difficulty' => 2,
            'options' => collect(['A', 'B', 'C', 'D', 'E'])->map(fn (string $label): array => [
                'label' => $label,
                'body' => "Isi {$label}",
                'is_correct' => $label === 'B',
                'weight' => $label === 'B' ? 5 : 0,
            ])->all(),
        ];
    }

    public function test_rich_text_formatting_is_kept_and_unsafe_markup_is_removed(): void
    {
        $body = '<p>Air adalah H<sub>2</sub>O dan <strong>penting</strong>.</p><script>alert(1)</script><p onclick="x()">Halo</p>';

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post('/admin/questions', $this->payload($this->subtest(), $body, '<p><em>Karena</em> itu <sup>2</sup></p>'))
            ->assertRedirect('/admin/questions');

        $question = Question::sole();

        $this->assertStringContainsString('H<sub>2</sub>O', $question->body);
        $this->assertStringContainsString('<strong>penting</strong>', $question->body);
        $this->assertStringNotContainsString('script', $question->body);
        $this->assertStringNotContainsString('onclick', $question->body);
        $this->assertStringContainsString('<em>Karena</em>', $question->explanation);
    }

    public function test_question_with_only_markup_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post('/admin/questions', $this->payload($this->subtest(), '<p><br></p>'))
            ->assertSessionHasErrors('body');

        $this->assertSame(0, Question::count());
    }

    public function test_empty_explanation_is_stored_as_null(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post('/admin/questions', $this->payload($this->subtest(), '<p>Soal biasa</p>', '<p><br></p>'))
            ->assertRedirect('/admin/questions');

        $this->assertNull(Question::sole()->explanation);
    }
}
