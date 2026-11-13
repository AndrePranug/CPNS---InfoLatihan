<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Services\ProgressService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultController extends Controller
{
    public function __construct(
        private readonly ReviewService $review,
        private readonly ProgressService $progress,
    ) {}

    public function show(Request $request, Attempt $attempt): Response
    {
        abort_unless($attempt->user_id === $request->user()->id, 403);
        abort_unless($attempt->isSubmitted(), 404);

        $attempt->load('answers.option', 'answers.question.subtest', 'answers.question.topic');

        $subtests = $attempt->tryout->questions()
            ->with('subtest')
            ->get()
            ->groupBy(fn ($question) => $question->subtest->code->value)
            ->map(fn ($questions, string $code): array => [
                'code' => $code,
                'name' => $questions->first()->subtest->name,
                'score' => $attempt->{'score_'.strtolower($code)},
                'passing_grade' => $questions->first()->subtest->passing_grade,
            ])->values();

        $topicScores = $attempt->answers
            ->filter(fn ($answer) => $answer->question !== null)
            ->groupBy(fn ($answer) => $answer->question->topic?->name ?? 'Lainnya')
            ->map(fn ($answers, string $topic): array => [
                'topic' => $topic,
                'correct' => $answers->filter(fn ($a) => $a->option?->is_correct || ($a->option?->weight ?? 0) >= 4)->count(),
                'total' => $answers->count(),
            ])->sortBy('correct')->values();

        return Inertia::render('Results/Show', [
            'result' => [
                'attempt_id' => $attempt->id,
                'title' => $attempt->tryout->title,
                'total_score' => $attempt->total_score,
                'is_passed' => $attempt->is_passed,
                'submitted_at' => $attempt->submitted_at?->toIso8601String(),
                'subtests' => $subtests,
                'topics' => $topicScores,
            ],
            'review' => $this->review->forAttempt($attempt),
            'ranking' => $this->progress->tryoutRanking($attempt->tryout, $request->user()),
        ]);
    }
}
