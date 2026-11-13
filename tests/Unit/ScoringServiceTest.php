<?php

namespace Tests\Unit;

use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Tryout;
use App\Models\User;
use App\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_twk_is_five_or_zero_and_tkp_uses_option_weight(): void
    {
        $user = User::factory()->create();
        $twk = Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 5]);
        $tkp = Subtest::create(['code' => 'TKP', 'name' => 'TKP', 'question_count' => 45, 'passing_grade' => 3]);

        $twkQuestion = Question::create(['subtest_id' => $twk->id, 'body' => 'TWK?']);
        $twkWrong = Option::create(['question_id' => $twkQuestion->id, 'label' => 'B', 'body' => 'salah', 'is_correct' => false, 'weight' => 0]);

        $tkpQuestion = Question::create(['subtest_id' => $tkp->id, 'body' => 'TKP?']);
        $tkpBest = Option::create(['question_id' => $tkpQuestion->id, 'label' => 'A', 'body' => 'terbaik', 'is_correct' => false, 'weight' => 4]);

        $tryout = Tryout::create(['title' => 'Paket', 'duration_minutes' => 100]);
        $attempt = Attempt::create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
            'ends_at' => now()->addMinutes(100),
        ]);
        $attempt->answers()->create(['question_id' => $twkQuestion->id, 'option_id' => $twkWrong->id, 'answered_at' => now()]);
        $attempt->answers()->create(['question_id' => $tkpQuestion->id, 'option_id' => $tkpBest->id, 'answered_at' => now()]);

        $result = app(ScoringService::class)->submit($attempt);

        $this->assertSame(0, $result['twk']);
        $this->assertSame(4, $result['tkp']);
        $this->assertSame(4, $result['total']);
        $this->assertFalse($result['passed']);
    }
}
