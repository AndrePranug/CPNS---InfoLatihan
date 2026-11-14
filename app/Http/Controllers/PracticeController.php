<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Services\PracticeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PracticeController extends Controller
{
    public function __construct(private readonly PracticeService $practice) {}

    public function index(Request $request): Response
    {
        $mode = $request->query('mode') === Question::EXAM_SKB ? Question::EXAM_SKB : Question::EXAM_SKD;
        $subtest = $request->query('subtest');
        $topicId = $request->query('topic');
        $formationId = $request->query('formation');

        return Inertia::render('Practice/Index', [
            'questions' => $this->practice->questions(
                $mode,
                is_string($subtest) ? $subtest : null,
                is_numeric($topicId) ? (int) $topicId : null,
                is_numeric($formationId) ? (int) $formationId : null,
            ),
            'subtests' => $this->practice->subtests(),
            'topics' => $this->practice->topics(),
            'formations' => $this->practice->formations(),
            'filters' => [
                'mode' => $mode,
                'subtest' => $mode === Question::EXAM_SKD && is_string($subtest) ? $subtest : null,
                'topic' => $mode === Question::EXAM_SKD && is_numeric($topicId) ? (int) $topicId : null,
                'formation' => $mode === Question::EXAM_SKB && is_numeric($formationId) ? (int) $formationId : null,
            ],
        ]);
    }

    public function check(Request $request, Question $question): JsonResponse
    {
        $data = $request->validate([
            'option_id' => ['required', 'integer'],
        ]);

        return response()->json($this->practice->check($question, $data['option_id']));
    }
}
