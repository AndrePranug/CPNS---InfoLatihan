<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subtest_id' => ['required', 'exists:subtests,id'],
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
