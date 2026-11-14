<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Tryout;
use App\Services\ExamService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamService $exam,
        private readonly PaymentService $payments,
    ) {}

    public function start(Request $request, Tryout $tryout): RedirectResponse
    {
        abort_unless($tryout->isOpen(), 404);

        if (! $this->payments->hasAccess($request->user(), $tryout)) {
            if (! $this->payments->available()) {
                throw ValidationException::withMessages([
                    'attempt' => 'Paket ini berbayar dan pembayaran sedang dinonaktifkan. Coba lagi nanti.',
                ]);
            }

            return redirect()->route('checkout.show', $tryout);
        }

        $attempt = $this->exam->start($request->user(), $tryout);

        return redirect()->route('exam.show', $attempt);
    }

    public function show(Request $request, Attempt $attempt): Response|RedirectResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->isSubmitted()) {
            return redirect()->route('results.show', $attempt);
        }

        [$attempt, $questions] = $this->exam->payload($attempt);

        if ($attempt->isSubmitted()) {
            return redirect()->route('results.show', $attempt);
        }

        return Inertia::render('Exam/Attempt', [
            'attempt' => [
                'id' => $attempt->id,
                'tryout_title' => $attempt->tryout->title,
                'remaining_seconds' => $this->exam->remainingSeconds($attempt),
                'submit_url' => route('exam.submit', $attempt),
                'answer_url' => route('exam.answer', $attempt),
            ],
            'questions' => $questions,
        ]);
    }

    public function saveAnswer(Request $request, Attempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $attempt);

        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'option_id' => ['nullable', 'integer'],
            'is_flagged' => ['required', 'boolean'],
        ]);

        $this->exam->saveAnswer($attempt, $data['question_id'], $data['option_id'] ?? null, $data['is_flagged']);

        return response()->json(['saved_at' => now()->toIso8601String()]);
    }

    public function submit(Request $request, Attempt $attempt): RedirectResponse
    {
        $this->authorizeAttempt($request, $attempt);

        $this->exam->submit($attempt);

        return redirect()->route('results.show', $attempt);
    }

    private function authorizeAttempt(Request $request, Attempt $attempt): void
    {
        abort_unless($attempt->user_id === $request->user()->id, 403);
    }
}
