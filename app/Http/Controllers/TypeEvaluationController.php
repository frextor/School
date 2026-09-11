<?php

namespace App\Http\Controllers;

use App\Models\TypeEvaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Portage de `add_type_evaluation()` / `update_type_evaluation()` / `delete_type_evaluation()`. */
class TypeEvaluationController extends Controller
{
    public function index(): View
    {
        return view('types-evaluation.index', ['types' => TypeEvaluation::orderBy('type')->paginate(25)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:50']]);

        TypeEvaluation::create($data);

        return redirect()->route('types-evaluation.index')->with('status', 'Type d\'évaluation créé.');
    }

    public function update(Request $request, TypeEvaluation $type): RedirectResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:50']]);

        $type->update($data);

        return redirect()->route('types-evaluation.index')->with('status', 'Type d\'évaluation mis à jour.');
    }

    public function destroy(TypeEvaluation $type): RedirectResponse
    {
        $type->delete();

        return redirect()->route('types-evaluation.index')->with('status', 'Type d\'évaluation supprimé.');
    }
}
