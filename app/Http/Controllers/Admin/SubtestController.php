<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subtest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubtestController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Subtests/Index', [
            'subtests' => Subtest::withCount(['questions', 'topics'])->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, Subtest $subtest): RedirectResponse
    {
        $data = $request->validate([
            'question_count' => ['required', 'integer', 'between:1,200'],
            'passing_grade' => ['required', 'integer', 'min:0'],
        ]);

        $subtest->update($data);

        return back()->with('success', "Parameter {$subtest->code->value} disimpan.");
    }
}
