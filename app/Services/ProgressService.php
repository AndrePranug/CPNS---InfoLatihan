<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\DailyActivity;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public const DAILY_TARGET_MINUTES = 20;

    /**
     * Record study time and answered questions for a day (today by default).
     */
    public function record(User $user, int $minutes, int $questions): void
    {
        $activity = DailyActivity::firstOrNew([
            'user_id' => $user->id,
            'activity_date' => now()->toDateString(),
        ]);

        $activity->study_minutes += $minutes;
        $activity->questions_answered += $questions;
        $activity->save();
    }

    /**
     * Record an attempt's effort once, at submit time.
     */
    public function recordAttempt(Attempt $attempt): void
    {
        if ($attempt->daily_activity_recorded) {
            return;
        }

        $minutes = max(1, (int) $attempt->started_at->diffInMinutes($attempt->submitted_at ?? now()));
        $this->record($attempt->user, $minutes, $attempt->answers()->count());

        $attempt->forceFill(['daily_activity_recorded' => true])->save();
    }

    /**
     * @return array{minutes: int, target: int, percent: int, days: array<int, array{label: string, date: string, done: bool, today: bool}>, streak: int}
     */
    public function dailyGoal(User $user): array
    {
        $start = now()->startOfWeek();
        $week = collect(range(0, 6))->map(fn (int $offset) => $start->copy()->addDays($offset));

        $minutesByDate = DailyActivity::query()
            ->where('user_id', $user->id)
            ->whereBetween('activity_date', [$week->first()->toDateString(), $week->last()->toDateString()])
            ->pluck('study_minutes', 'activity_date')
            ->mapWithKeys(fn ($minutes, $date): array => [substr((string) $date, 0, 10) => (int) $minutes]);

        $today = now()->toDateString();
        $todayMinutes = $minutesByDate[$today] ?? 0;

        return [
            'minutes' => $todayMinutes,
            'target' => self::DAILY_TARGET_MINUTES,
            'percent' => (int) min(100, round($todayMinutes / self::DAILY_TARGET_MINUTES * 100)),
            'days' => $week->map(fn ($day): array => [
                'label' => $day->translatedFormat('D'),
                'date' => $day->toDateString(),
                'done' => ($minutesByDate[$day->toDateString()] ?? 0) >= self::DAILY_TARGET_MINUTES,
                'today' => $day->toDateString() === $today,
            ])->all(),
            'streak' => $this->streak($user),
        ];
    }

    /**
     * Consecutive days (ending today or yesterday) that hit the target.
     */
    public function streak(User $user): int
    {
        $dates = DailyActivity::query()
            ->where('user_id', $user->id)
            ->where('study_minutes', '>=', self::DAILY_TARGET_MINUTES)
            ->orderByDesc('activity_date')
            ->limit(400)
            ->pluck('activity_date')
            ->map(fn ($date): string => substr((string) $date, 0, 10));

        $streak = 0;
        $cursor = now()->startOfDay();

        if ($dates->isEmpty() || $dates->first() !== $cursor->toDateString()) {
            if (! $dates->contains(now()->subDay()->toDateString())) {
                return 0;
            }

            $cursor = $cursor->subDay();
        }

        foreach ($dates as $date) {
            if ($date === $cursor->toDateString()) {
                $streak++;
                $cursor = $cursor->subDay();
            } elseif ($date < $cursor->toDateString()) {
                break;
            }
        }

        return $streak;
    }

    /**
     * Top scorers by best total score per user, across finished attempts.
     *
     * @return Collection<int, array{name: string, score: int, is_me: bool}>
     */
    public function leaderboard(User $user, int $limit = 5): Collection
    {
        return $this->bestScoresPerUser()
            ->take($limit)
            ->map(fn (object $row): array => [
                'name' => $row->name,
                'score' => (int) $row->best_score,
                'is_me' => $row->user_id === $user->id,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{user_id: int, name: string, best_score: int}>
     */
    private function bestScoresPerUser(): Collection
    {
        // ponytail: one query per call; add cache if dashboard gets hot.
        return DB::table('attempts')
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->whereNotNull('attempts.submitted_at')
            ->groupBy('attempts.user_id', 'users.name')
            ->orderByDesc('best_score')
            ->get([
                'attempts.user_id',
                'users.name',
                DB::raw('max(attempts.total_score) as best_score'),
            ]);
    }

    /**
     * The attempt the user can still resume, if any.
     *
     * @return array<string, mixed>|null
     */
    public function resumeAttempt(User $user): ?array
    {
        $attempt = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNull('submitted_at')
            ->where('ends_at', '>', now())
            ->with('tryout:id,title')
            ->latest('id')
            ->first();

        if ($attempt === null) {
            return null;
        }

        $answered = $attempt->answers()->whereNotNull('option_id')->count();
        $total = $attempt->tryout->questions()->count();

        return [
            'attempt_id' => $attempt->id,
            'title' => $attempt->tryout->title,
            'answered' => $answered,
            'total' => $total,
            'percent' => $total === 0 ? 0 : (int) round($answered / $total * 100),
            'remaining_minutes' => (int) now()->diffInMinutes($attempt->ends_at),
            'url' => route('exam.show', $attempt),
        ];
    }

    /**
     * Ranking of one tryout by best score per participant.
     *
     * @return array{position: int|null, total: int, rows: Collection<int, array{position: int, name: string, score: int, is_me: bool}>}
     */
    public function tryoutRanking(Tryout $tryout, User $user, int $limit = 10): array
    {
        $best = DB::table('attempts')
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->where('attempts.tryout_id', $tryout->id)
            ->whereNotNull('attempts.submitted_at')
            ->groupBy('attempts.user_id', 'users.name')
            ->orderByDesc('best_score')
            ->get([
                'attempts.user_id',
                'users.name',
                DB::raw('max(attempts.total_score) as best_score'),
            ]);

        $rows = $best->values()->map(fn (object $row, int $index): array => [
            'position' => $index + 1,
            'name' => $row->name,
            'score' => (int) $row->best_score,
            'is_me' => $row->user_id === $user->id,
        ]);

        return [
            'position' => $rows->first(fn (array $row): bool => $row['is_me'])['position'] ?? null,
            'total' => $rows->count(),
            'rows' => $rows->take($limit)->values(),
        ];
    }
}
