<?php

namespace App\Http\Controllers;

use App\Models\Tryout;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TryoutController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function index(): Response
    {
        $tryouts = Tryout::query()
            ->published()
            ->withCount('questions')
            ->latest()
            ->get()
            ->map(fn (Tryout $tryout): array => [
                'id' => $tryout->id,
                'title' => $tryout->title,
                'description' => $tryout->description,
                'duration_minutes' => $tryout->duration_minutes,
                'price' => $tryout->price,
                'question_count' => $tryout->questions_count,
                'is_open' => $tryout->isOpen(),
            ]);

        return Inertia::render('Tryouts/Index', [
            'tryouts' => $tryouts,
        ]);
    }

    public function show(Request $request, Tryout $tryout): Response
    {
        abort_unless($tryout->isOpen(), 404);

        return Inertia::render('Tryouts/Show', [
            'tryout' => [
                'id' => $tryout->id,
                'title' => $tryout->title,
                'description' => $tryout->description,
                'duration_minutes' => $tryout->duration_minutes,
                'price' => $tryout->price,
                'question_count' => $tryout->questions()->count(),
                'has_access' => $this->payments->hasAccess($request->user(), $tryout),
                'payments_available' => $this->payments->available(),
            ],
        ]);
    }
}
