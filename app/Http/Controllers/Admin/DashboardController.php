<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'completed_attempts' => Attempt::whereNotNull('submitted_at')->count(),
                'average_score' => (int) round(Attempt::whereNotNull('submitted_at')->avg('total_score') ?? 0),
                'published_tryouts' => Tryout::published()->count(),
                'questions' => Question::count(),
            ],
            'hardestQuestions' => $this->hardestQuestions(),
        ]);
    }

    /**
     * Questions with lowest correct rate across submitted attempts.
     *
     * @return Collection<int, array{id: int, body: string, subtest: string, answered: int, correct_rate: int}>
     */
    private function hardestQuestions(int $limit = 5): Collection
    {
        return AttemptAnswer::query()
            ->join('attempts', 'attempts.id', '=', 'attempt_answers.attempt_id')
            ->join('options', 'options.id', '=', 'attempt_answers.option_id')
            ->join('questions', 'questions.id', '=', 'attempt_answers.question_id')
            ->join('subtests', 'subtests.id', '=', 'questions.subtest_id')
            ->whereNotNull('attempts.submitted_at')
            ->groupBy('questions.id', 'questions.body', 'subtests.code')
            ->havingRaw('count(*) >= 1')
            ->orderByRaw('avg(case when options.is_correct then 1 else 0 end) asc')
            ->limit($limit)
            ->get([
                'questions.id',
                'questions.body',
                'subtests.code as subtest',
                DB::raw('count(*) as answered'),
                DB::raw('round(avg(case when options.is_correct then 1 else 0 end) * 100) as correct_rate'),
            ])
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'body' => str($row->body)->limit(110)->value(),
                'subtest' => (string) $row->subtest,
                'answered' => (int) $row->answered,
                'correct_rate' => (int) $row->correct_rate,
            ]);
    }
}
