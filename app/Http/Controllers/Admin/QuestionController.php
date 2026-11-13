<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use App\Services\QuestionImportService;
use App\Services\QuestionVersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('q')->trim()->value();

        $questions = Question::query()
            ->with(['subtest', 'topic'])
            ->when($search !== '', fn ($query) => $query->where('body', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Question $question): array => [
                'id' => $question->id,
                'body' => str($question->body)->limit(80),
                'subtest' => $question->subtest->code->value,
                'topic' => $question->topic?->name,
                'version' => $question->version,
            ]);

        return Inertia::render('Admin/Questions/Index', [
            'questions' => $questions,
            'filters' => ['q' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Questions/Form', [
            'subtests' => Subtest::all(['id', 'code', 'name']),
            'topics' => Topic::all(['id', 'subtest_id', 'name']),
        ]);
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $question = Question::create([
                'subtest_id' => $data['subtest_id'],
                'topic_id' => $data['topic_id'] ?? null,
                'body' => $data['body'],
                'explanation' => $data['explanation'] ?? null,
                'difficulty' => $data['difficulty'],
            ]);

            $question->options()->createMany(
                collect($data['options'])->map(fn (array $option): array => [
                    'label' => $option['label'],
                    'body' => $option['body'],
                    'is_correct' => (bool) ($option['is_correct'] ?? false),
                    'weight' => $option['weight'],
                ])->all(),
            );
        });

        return redirect()->route('admin.questions.index')->with('success', 'Soal ditambahkan.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return back()->with('success', 'Soal dihapus.');
    }

    public function importForm(): Response
    {
        return Inertia::render('Admin/Questions/Import', [
            'header' => 'subtest,topik,soal,pembahasan,kesulitan,a,b,c,d,e,kunci,bobot',
        ]);
    }

    public function import(Request $request, QuestionImportService $importer): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $result = $importer->import($request->file('file'));

        return back()->with('success', "{$result['imported']} soal diimpor.")
            ->with('import_errors', $result['errors']);
    }

    public function edit(Question $question): Response
    {
        $question->load('options');

        return Inertia::render('Admin/Questions/Form', [
            'subtests' => Subtest::all(['id', 'code', 'name']),
            'topics' => Topic::all(['id', 'subtest_id', 'name']),
            'question' => [
                'id' => $question->id,
                'subtest_id' => $question->subtest_id,
                'topic_id' => $question->topic_id,
                'body' => $question->body,
                'explanation' => $question->explanation,
                'difficulty' => $question->difficulty,
                'version' => $question->version,
                'options' => $question->options->sortBy('label')->values()->map(fn ($option): array => [
                    'label' => $option->label,
                    'body' => $option->body,
                    'is_correct' => $option->is_correct,
                    'weight' => $option->weight,
                ]),
            ],
        ]);
    }

    public function revise(Question $question, QuestionVersionService $versions): RedirectResponse
    {
        $new = $versions->revise($question);

        return redirect()->route('admin.questions.edit', $new)->with('success', "Versi baru v{$new->version} dibuat. Versi lama diarsipkan.");
    }
}
