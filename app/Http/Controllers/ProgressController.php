<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\DailyActivity;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgressController extends Controller
{
    public function __construct(private readonly ProgressService $progress) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $attempts = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->with('tryout:id,title')
            ->oldest('submitted_at')
            ->get();

        $trend = $attempts->take(-20)->values()->map(fn (Attempt $attempt): array => [
            'label' => $attempt->submitted_at?->translatedFormat('d M'),
            'total' => $attempt->total_score,
            'twk' => $attempt->score_twk,
            'tiu' => $attempt->score_tiu,
            'tkp' => $attempt->score_tkp,
        ]);

        $weakTopics = $attempts
            ->flatMap(fn (Attempt $attempt) => $attempt->answers()
                ->with(['question.topic', 'option'])
                ->get()
                ->filter(fn ($answer) => $answer->question?->topic !== null)
                ->map(fn ($answer): array => [
                    'topic' => $answer->question->topic->name,
                    'correct' => $answer->option?->is_correct ? 1 : 0,
                ]))
            ->groupBy('topic')
            ->map(fn ($rows, string $topic): array => [
                'topic' => $topic,
                'correct' => $rows->sum('correct'),
                'total' => $rows->count(),
                'percent' => (int) round($rows->sum('correct') / max(1, $rows->count()) * 100),
            ])
            ->sortBy('percent')
            ->take(8)
            ->values();

        $weeklyMinutes = DailyActivity::query()
            ->where('user_id', $user->id)
            ->where('activity_date', '>=', now()->subDays(13)->toDateString())
            ->orderBy('activity_date')
            ->get()
            ->map(fn (DailyActivity $activity): array => [
                'date' => $activity->activity_date->translatedFormat('d M'),
                'minutes' => $activity->study_minutes,
            ]);

        $best = $attempts->max('total_score') ?? 0;
        $average = $attempts->isEmpty() ? 0 : (int) round($attempts->avg('total_score'));
        $first = $attempts->first()?->total_score ?? 0;
        $last = $attempts->last()?->total_score ?? 0;

        return Inertia::render('Progress/Index', [
            'summary' => [
                'attempts' => $attempts->count(),
                'best' => (int) $best,
                'average' => $average,
                'improvement' => $first === 0 ? 0 : (int) round(($last - $first) / $first * 100),
                'streak' => $this->progress->streak($user),
            ],
            'trend' => $trend,
            'weakTopics' => $weakTopics,
            'activity' => $weeklyMinutes,
        ]);
    }
}
