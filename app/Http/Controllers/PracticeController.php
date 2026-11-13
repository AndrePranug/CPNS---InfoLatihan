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
        $subtest = $request->query('subtest');
        $topicId = $request->query('topic');

        return Inertia::render('Practice/Index', [
            'questions' => $this->practice->questions(
                is_string($subtest) ? $subtest : null,
                is_numeric($topicId) ? (int) $topicId : null,
            ),
            'subtests' => $this->practice->subtests(),
            'topics' => $this->practice->topics(),
            'filters' => [
                'subtest' => $subtest,
                'topic' => $topicId === null ? null : (int) $topicId,
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
