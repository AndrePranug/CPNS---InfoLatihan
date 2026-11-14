<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FormationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Formations/Index', [
            'formations' => Formation::query()
                ->withCount('questions')
                ->orderBy('name')
                ->get()
                ->map(fn (Formation $formation): array => [
                    'id' => $formation->id,
                    'name' => $formation->name,
                    'question_count' => $formation->questions_count,
                    'is_active' => $formation->is_active,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('formations', 'name')],
        ]);

        Formation::create([...$data, 'is_active' => true]);

        return back()->with('success', 'Formasi SKB dibuat.');
    }

    public function update(Formation $formation): RedirectResponse
    {
        $formation->update(['is_active' => ! $formation->is_active]);

        return back()->with('success', $formation->is_active ? 'Formasi diaktifkan.' : 'Formasi dinonaktifkan.');
    }
}
