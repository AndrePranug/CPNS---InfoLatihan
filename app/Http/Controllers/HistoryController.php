<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function index(Request $request): Response
    {
        $attempts = Attempt::query()
            ->where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at')
            ->with('tryout:id,title')
            ->latest('submitted_at')
            ->paginate(10)
            ->through(fn (Attempt $attempt): array => [
                'id' => $attempt->id,
                'title' => $attempt->tryout->title,
                'score' => $attempt->total_score,
                'is_passed' => $attempt->is_passed,
                'submitted_at' => $attempt->submitted_at?->translatedFormat('d M Y H:i'),
                'result_url' => route('results.show', $attempt),
            ]);

        $scores = Attempt::query()
            ->where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at')
            ->oldest('submitted_at')
            ->limit(20)
            ->pluck('total_score');

        return Inertia::render('History/Index', [
            'attempts' => $attempts,
            'trend' => $scores,
        ]);
    }
}
