<?php

namespace App\Http\Requests\Admin;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->mergeIfMissing(['exam_type' => Question::EXAM_SKD]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'exam_type' => ['required', Rule::in([Question::EXAM_SKD, Question::EXAM_SKB])],
            'subtest_id' => ['nullable', 'required_if:exam_type,skd', 'exists:subtests,id'],
            'formation_id' => ['nullable', 'required_if:exam_type,skb', 'exists:formations,id'],
            'topic_id' => ['nullable', 'exists:topics,id'],
            'body' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['required', 'integer', 'between:1,3'],
            'options' => ['required', 'array', 'size:5'],
            'options.*.label' => ['required', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'options.*.body' => ['required', 'string'],
            'options.*.is_correct' => ['boolean'],
            'options.*.weight' => ['required', 'integer', 'between:0,5'],
        ];
    }
}
