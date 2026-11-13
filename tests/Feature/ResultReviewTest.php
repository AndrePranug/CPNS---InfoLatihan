<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_exposes_keys_only_after_submit(): void
    {
        $user = User::factory()->create();
        $subtest = Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 5]);
        $question = Question::create(['subtest_id' => $subtest->id, 'body' => 'Soal?', 'explanation' => 'Penjelasan.']);

        foreach (['A', 'B'] as $index => $label) {
            Option::create([
                'question_id' => $question->id,
                'label' => $label,
                'body' => "Pilihan {$label}",
                'is_correct' => $index === 0,
                'weight' => $index === 0 ? 5 : 0,
            ]);
        }

        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100, 'is_published' => true]);
        $tryout->questions()->attach($question->id, ['position' => 1]);

        $attempt = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addMinutes(100),
        ]);

        $wrong = $question->options()->where('label', 'B')->sole();
        $attempt->answers()->create(['question_id' => $question->id, 'option_id' => $wrong->id, 'is_flagged' => true, 'answered_at' => now()]);

        $this->actingAs($user)->get("/attempts/{$attempt->id}/result")->assertNotFound();

        app(ScoringService::class)->submit($attempt);

        $page = $this->actingAs($user)->get("/attempts/{$attempt->id}/result")->assertOk()->viewData('page')['props'];

        $this->assertCount(1, $page['review']);
        $this->assertSame('salah', $page['review'][0]['status']);
        $this->assertTrue($page['review'][0]['is_flagged']);
        $this->assertSame('Penjelasan.', $page['review'][0]['explanation']);
        $this->assertTrue(collect($page['review'][0]['options'])->firstWhere('label', 'A')['is_correct']);
    }
}
