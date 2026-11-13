<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Tryout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TryoutController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Tryouts/Index', [
            'tryouts' => Tryout::withCount('questions')->latest('id')->get()
                ->map(fn (Tryout $tryout): array => [
                    'id' => $tryout->id,
                    'title' => $tryout->title,
                    'duration_minutes' => $tryout->duration_minutes,
                    'price' => $tryout->price,
                    'is_published' => $tryout->is_published,
                    'question_count' => $tryout->questions_count,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'between:1,600'],
            'price' => ['required', 'integer', 'min:0'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'is_published' => ['boolean'],
        ]);

        Tryout::create($data);

        return back()->with('success', 'Paket tryout dibuat.');
    }

    public function update(Request $request, Tryout $tryout): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'between:1,600'],
            'price' => ['required', 'integer', 'min:0'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'is_published' => ['boolean'],
        ]);

        $tryout->update($data);

        return back()->with('success', 'Paket diperbarui.');
    }

    public function edit(Tryout $tryout): Response
    {
        $selected = $tryout->questions()
            ->orderBy('tryout_question.position')
            ->get(['questions.id'])
            ->pluck('id')
            ->all();

        $bank = Question::query()
            ->with('subtest:id,code')
            ->withCount('options')
            ->orderBy('subtest_id')
            ->orderBy('id')
            ->get()
            ->map(fn (Question $question): array => [
                'id' => $question->id,
                'subtest' => $question->subtest->code->value,
                'body' => str($question->body)->limit(110)->value(),
                'version' => $question->version,
                'options_count' => $question->options_count,
                'valid' => $question->options_count === 5,
            ]);

        return Inertia::render('Admin/Tryouts/Edit', [
            'tryout' => [
                'id' => $tryout->id,
                'title' => $tryout->title,
                'description' => $tryout->description,
                'duration_minutes' => $tryout->duration_minutes,
                'price' => $tryout->price,
                'opens_at' => $tryout->opens_at?->format('Y-m-d\TH:i'),
                'closes_at' => $tryout->closes_at?->format('Y-m-d\TH:i'),
                'is_published' => $tryout->is_published,
            ],
            'selected' => $selected,
            'bank' => $bank,
        ]);
    }

    public function syncQuestions(Request $request, Tryout $tryout): RedirectResponse
    {
        $data = $request->validate([
            'question_ids' => ['array'],
            'question_ids.*' => [Rule::exists('questions', 'id')],
        ]);

        $ids = collect($data['question_ids'] ?? [])->unique()->values();

        if ($ids->isEmpty() && ! $request->boolean('confirm_empty')) {
            return back()->with('error', 'Paket tanpa soal tidak bisa diterbitkan. Centang konfirmasi bila memang ingin mengosongkan.');
        }

        $pivot = $ids
            ->mapWithKeys(fn (int $id, int $index): array => [$id => ['position' => $index + 1]])
            ->all();

        $tryout->questions()->sync($pivot);

        return back()->with('success', $ids->count().' soal tersimpan di paket.');
    }

    public function destroy(Tryout $tryout): RedirectResponse
    {
        $tryout->delete();

        return redirect()->route('admin.tryouts.index')->with('success', 'Paket diarsipkan.');
    }
}
