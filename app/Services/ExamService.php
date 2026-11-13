<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Question;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamService
{
    public function __construct(
        private readonly ScoringService $scoring,
        private readonly ProgressService $progress,
        private readonly ExamOrderService $order,
    ) {}

    /**
     * Start (or resume) the single active attempt for this user and tryout.
     */
    public function start(User $user, Tryout $tryout): Attempt
    {
        return DB::transaction(function () use ($user, $tryout): Attempt {
            $active = Attempt::query()
                ->where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->whereNull('submitted_at')
                ->latest('id')
                ->first();

            if ($active) {
                if ($active->isExpired()) {
                    $this->submit($active);

                    throw ValidationException::withMessages([
                        'attempt' => 'Waktu percobaan sebelumnya sudah habis dan sudah dinilai.',
                    ]);
                }

                return $active;
            }

            $now = now();

            return Attempt::create([
                'user_id' => $user->id,
                'tryout_id' => $tryout->id,
                'started_at' => $now,
                'ends_at' => $now->copy()->addMinutes($tryout->duration_minutes),
            ]);
        });
    }

    /**
     * Questions of an attempt in shuffled-per-attempt order, ordered by the stored position.
     * Answer keys are never included.
     *
     * @return array{0: Attempt, 1: array<int, array<string, mixed>>}
     */
    public function payload(Attempt $attempt): array
    {
        if (! $attempt->isSubmitted() && $attempt->isExpired()) {
            $this->scoring->submit($attempt);
            $attempt->refresh();
        }

        $questions = $attempt->tryout
            ->questions()
            ->orderBy('tryout_question.position')
            ->get(['questions.id', 'questions.subtest_id', 'questions.body', 'questions.image_path']);

        $ordered = $this->order->shuffle($questions->pluck('id')->all(), $attempt, 'question');
        $questions = $questions->keyBy('id');

        $answers = $attempt->answers()->get()->keyBy('question_id');

        return [
            $attempt,
            collect($ordered)->map(function (int $questionId) use ($questions, $answers, $attempt): array {
                /** @var Question $question */
                $question = $questions[$questionId];
                $answer = $answers->get($question->id);

                $optionIds = $this->order->shuffle(
                    $question->options()->pluck('id')->all(),
                    $attempt,
                    'option-'.$question->id,
                );

                $options = $question->options()
                    ->get(['id', 'label', 'body'])
                    ->keyBy('id');

                return [
                    'id' => $question->id,
                    'subtest' => $question->subtest()->value('code'),
                    'body' => $question->body,
                    'image_path' => $question->image_path,
                    'options' => collect($optionIds)->map(fn (int $id): array => [
                        'id' => $options[$id]->id,
                        'label' => $options[$id]->label,
                        'body' => $options[$id]->body,
                    ])->all(),
                    'selected_option_id' => $answer?->option_id,
                    'is_flagged' => (bool) $answer?->is_flagged,
                ];
            })->values()->all(),
        ];
    }

    public function submit(Attempt $attempt): Attempt
    {
        if ($attempt->isSubmitted()) {
            return $attempt;
        }

        $this->scoring->submit($attempt);
        $attempt->refresh();
        $this->progress->recordAttempt($attempt);

        return $attempt;
    }

    /**
     * Persist one answer without reloading the page.
     */
    public function saveAnswer(Attempt $attempt, int $questionId, ?int $optionId, bool $flagged): void
    {
        if ($attempt->isSubmitted() || $attempt->isExpired()) {
            if ($attempt->isExpired() && ! $attempt->isSubmitted()) {
                $this->scoring->submit($attempt);
            }

            throw ValidationException::withMessages([
                'attempt' => 'Waktu ujian sudah berakhir.',
            ]);
        }

        $belongs = $attempt->tryout->questions()->whereKey($questionId)->exists();

        if (! $belongs) {
            abort(404);
        }

        if ($optionId !== null) {
            $optionBelongs = $attempt->tryout->questions()
                ->whereKey($questionId)
                ->whereHas('options', fn ($query) => $query->whereKey($optionId))
                ->exists();

            if (! $optionBelongs) {
                abort(404);
            }
        }

        $attempt->answers()->updateOrCreate(
            ['question_id' => $questionId],
            [
                'option_id' => $optionId,
                'is_flagged' => $flagged,
                'answered_at' => $optionId === null ? null : now(),
            ],
        );
    }

    /**
     * @return array<string, int>
     */
    public function remainingSeconds(Attempt $attempt): int
    {
        return max(0, (int) now()->diffInSeconds($attempt->ends_at, false));
    }
}
