<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionVersionService
{
    /**
     * Duplicate a question as a new version and freeze the old one.
     * Refuses when the question is still used by an unfinished attempt, so live exams keep stable keys.
     */
    public function revise(Question $question): Question
    {
        $inUse = DB::table('attempt_answers')
            ->join('attempts', 'attempts.id', '=', 'attempt_answers.attempt_id')
            ->where('attempt_answers.question_id', $question->id)
            ->whereNull('attempts.submitted_at')
            ->exists();

        if ($inUse) {
            throw ValidationException::withMessages([
                'question' => 'Soal sedang dipakai percobaan aktif. Tunggu sampai peserta selesai.',
            ]);
        }

        return DB::transaction(function () use ($question): Question {
            $new = $question->replicate();
            $new->version = $question->version + 1;
            $new->save();

            foreach ($question->options as $option) {
                $new->options()->create($option->only(['label', 'body', 'is_correct', 'weight']));
            }

            $question->delete();

            return $new->load('options');
        });
    }
}
