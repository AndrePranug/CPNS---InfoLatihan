<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use Illuminate\Support\Collection;

/**
 * Stateless practice mode: no attempt, no timer, no persistence.
 * Answer keys are only returned after the user checks an answer for that question.
 */
class PracticeService
{
    /**
     * @return Collection<int, array{id: int, body: string, image_path: string|null, label: string, topic: string|null, options: array<int, array{id: int, body: string}>}>
     */
    public function questions(
        string $mode = Question::EXAM_SKD,
        ?string $subtestCode = null,
        ?int $topicId = null,
        ?int $formationId = null,
        int $limit = 10,
    ): Collection {
        $mode = $mode === Question::EXAM_SKB ? Question::EXAM_SKB : Question::EXAM_SKD;

        return Question::query()
            ->with(['subtest:id,code', 'topic:id,name', 'formation:id,name'])
            ->where('exam_type', $mode)
            ->when($mode === Question::EXAM_SKD && $subtestCode, fn ($query) => $query->whereHas('subtest', fn ($q) => $q->where('code', $subtestCode)))
            ->when($mode === Question::EXAM_SKD && $topicId, fn ($query) => $query->where('topic_id', $topicId))
            ->when($mode === Question::EXAM_SKB && $formationId, fn ($query) => $query->where('formation_id', $formationId))
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(fn (Question $question): array => [
                'id' => $question->id,
                'body' => $question->body,
                'image_path' => $question->image_path,
                'label' => $mode === Question::EXAM_SKB
                    ? ($question->formation?->name ?? 'SKB')
                    : $question->subtest->code->value,
                'topic' => $question->topic?->name,
                'options' => $question->options()
                    ->orderBy('label')
                    ->get(['id', 'body'])
                    ->map(fn ($option): array => ['id' => $option->id, 'body' => $option->body])
                    ->all(),
            ]);
    }

    /**
     * Check one answer and reveal its key.
     *
     * @return array{is_correct: bool, correct_option_id: int|null, weight: int|null, explanation: string|null}
     */
    public function check(Question $question, int $optionId): array
    {
        $options = $question->options()->get(['id', 'is_correct', 'weight']);
        $chosen = $options->firstWhere('id', $optionId);

        abort_if($chosen === null, 404);

        $correct = $options->firstWhere('is_correct', true);

        return [
            'is_correct' => (bool) $chosen->is_correct,
            'correct_option_id' => $correct?->id,
            'weight' => $chosen->weight,
            'explanation' => $question->explanation,
        ];
    }

    /**
     * @return Collection<int, array{code: string, name: string, question_count: int}>
     */
    public function subtests(): Collection
    {
        return Subtest::query()
            ->withCount(['questions' => fn ($query) => $query->where('exam_type', Question::EXAM_SKD)])
            ->orderBy('id')
            ->get()
            ->map(fn (Subtest $subtest): array => [
                'code' => $subtest->code->value,
                'name' => $subtest->name,
                'question_count' => $subtest->questions_count,
            ]);
    }

    /**
     * @return Collection<int, array{id: int, name: string, subtest: string, question_count: int}>
     */
    public function topics(): Collection
    {
        return Topic::query()
            ->with('subtest:id,code')
            ->withCount(['questions' => fn ($query) => $query->where('exam_type', Question::EXAM_SKD)])
            ->whereHas('questions', fn ($query) => $query->where('exam_type', Question::EXAM_SKD))
            ->orderBy('subtest_id')
            ->get()
            ->map(fn (Topic $topic): array => [
                'id' => $topic->id,
                'name' => $topic->name,
                'subtest' => $topic->subtest->code->value,
                'question_count' => $topic->questions_count,
            ]);
    }

    /**
     * @return Collection<int, array{id: int, name: string, question_count: int}>
     */
    public function formations(): Collection
    {
        return Formation::query()
            ->where('is_active', true)
            ->withCount(['questions' => fn ($query) => $query->where('exam_type', Question::EXAM_SKB)])
            ->orderBy('name')
            ->get()
            ->map(fn (Formation $formation): array => [
                'id' => $formation->id,
                'name' => $formation->name,
                'question_count' => $formation->questions_count,
            ]);
    }
}
