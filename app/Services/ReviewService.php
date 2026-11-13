<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;

class ReviewService
{
    /**
     * Build review entries for a submitted attempt. Answer keys are only exposed here, after submit.
     *
     * @return array<int, array<string, mixed>>
     */
    public function forAttempt(Attempt $attempt): array
    {
        $attempt->load('answers.option', 'answers.question.options', 'answers.question.subtest', 'answers.question.topic');

        $questions = $attempt->tryout->questions()
            ->orderBy('tryout_question.position')
            ->get();

        $answers = $attempt->answers->keyBy('question_id');

        return $questions->map(function (Question $question) use ($answers): array {
            $answer = $answers->get($question->id);
            $correct = $question->options->firstWhere('is_correct', true);

            return [
                'id' => $question->id,
                'body' => $question->body,
                'image_path' => $question->image_path,
                'subtest' => $question->subtest->code->value,
                'topic' => $question->topic?->name,
                'explanation' => $question->explanation,
                'is_flagged' => (bool) $answer?->is_flagged,
                'options' => $question->options->sortBy('label')->values()->map(fn ($option): array => [
                    'id' => $option->id,
                    'label' => $option->label,
                    'body' => $option->body,
                    'is_correct' => (bool) $option->is_correct,
                    'weight' => $option->weight,
                    'is_selected' => $answer?->option_id === $option->id,
                ])->all(),
                'selected_option_id' => $answer?->option_id,
                'correct_option_id' => $correct?->id,
                'status' => $this->status($answer?->option, $answer?->question?->subtest?->code->value ?? $question->subtest->code->value),
            ];
        })->values()->all();
    }

    private function status(?Option $option, string $subtestCode): string
    {
        if ($option === null) {
            return 'kosong';
        }

        if ($subtestCode === 'TKP') {
            return $option->weight >= 4 ? 'benar' : ($option->weight > 0 ? 'sebagian' : 'salah');
        }

        return $option->is_correct ? 'benar' : 'salah';
    }
}
