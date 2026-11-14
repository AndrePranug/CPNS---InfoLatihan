<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Subtest;
use App\Notifications\AttemptResultNotification;
use Illuminate\Support\Collection;

class ScoringService
{
    /**
     * Score an attempt from its stored answers and persist the result.
     *
     * @return array{twk: int, tiu: int, tkp: int, total: int, passed: bool}
     */
    public function submit(Attempt $attempt): array
    {
        $wasSubmitted = $attempt->submitted_at !== null;

        $attempt->load('answers.option', 'answers.question.subtest');

        $scores = ['TWK' => 0, 'TIU' => 0, 'TKP' => 0];

        foreach ($attempt->answers as $answer) {
            if ($answer->option === null || $answer->question === null) {
                continue;
            }

            $code = $answer->question->subtest->code->value;
            $scores[$code] += $answer->question->subtest->code->value === 'TKP'
                ? $answer->option->weight
                : ($answer->option->is_correct ? 5 : 0);
        }

        $passing = $this->passingGrades();

        $passed = $scores['TWK'] >= $passing['TWK']
            && $scores['TIU'] >= $passing['TIU']
            && $scores['TKP'] >= $passing['TKP'];

        $attempt->update([
            'submitted_at' => now(),
            'score_twk' => $scores['TWK'],
            'score_tiu' => $scores['TIU'],
            'score_tkp' => $scores['TKP'],
            'total_score' => $scores['TWK'] + $scores['TIU'] + $scores['TKP'],
            'is_passed' => $passed,
        ]);

        if (! $wasSubmitted) {
            $attempt->user->notify(new AttemptResultNotification($attempt));
        }

        return [
            'twk' => $scores['TWK'],
            'tiu' => $scores['TIU'],
            'tkp' => $scores['TKP'],
            'total' => $attempt->total_score,
            'passed' => $passed,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function passingGrades(): array
    {
        /** @var Collection<string, int> $grades */
        $grades = Subtest::query()->pluck('passing_grade', 'code');

        return [
            'TWK' => $grades['TWK'] ?? 65,
            'TIU' => $grades['TIU'] ?? 80,
            'TKP' => $grades['TKP'] ?? 166,
        ];
    }
}
