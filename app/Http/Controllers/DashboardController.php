<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Subtest;
use App\Models\Tryout;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly ProgressService $progress) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $best = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->orderByDesc('total_score')
            ->value('total_score') ?? 0;

        return Inertia::render('Dashboard', [
            'stats' => [
                'completed' => Attempt::query()->where('user_id', $user->id)->whereNotNull('submitted_at')->count(),
                'attempts' => $user->attempts()->count(),
                'available' => Tryout::query()->published()->count(),
                'best_score' => (int) $best,
            ],
            'resume' => $this->progress->resumeAttempt($user),
            'goal' => $this->progress->dailyGoal($user),
            'leaderboards' => [
                'week' => $this->progress->leaderboard($user, 5, 'week'),
                'month' => $this->progress->leaderboard($user, 5, 'month'),
            ],
            'missions' => $this->progress->missions($user),
            'subtests' => Subtest::query()->withCount('questions')->orderBy('id')->get()
                ->map(fn (Subtest $subtest): array => [
                    'code' => $subtest->code->value,
                    'name' => $subtest->name,
                    'question_count' => $subtest->questions_count,
                ]),
        ]);
    }
}
