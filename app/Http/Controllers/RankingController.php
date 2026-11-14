<?php

namespace App\Http\Controllers;

use App\Services\ProgressService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function __construct(private readonly ProgressService $progress) {}

    public function index(Request $request): Response
    {
        $period = $request->string('period')->toString();
        $period = in_array($period, ['week', 'month'], true) ? $period : 'all';

        return Inertia::render('Rankings/Index', [
            'ranking' => $this->progress->nationalRanking($request->user(), $period),
        ]);
    }
}
