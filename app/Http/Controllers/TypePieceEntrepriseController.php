<?php

namespace App\Http\Controllers;

use App\Models\TypePieceEntreprise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de "Types de documents" dans `Referentiel.php`
 * (get_types_document / add_type_document / update_type_document / supp_type_document).
 */
class TypePieceEntrepriseController extends Controller
{
    public function index(): View
    {
        $types = TypePieceEntreprise::orderBy('type')->paginate(25);

        return view('referentiel.types-piece.index', ['types' => $types]);
    }

    public function create(): View
    {
        return view('referentiel.types-piece.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:255']]);

        TypePieceEntreprise::create([
            'type' => $data['type'],
            'periode_associee' => $request->boolean('periode_associee'),
        ]);

        return redirect()
            ->route('referentiel.types-piece.index')
            ->with('status', 'Type de document créé avec succès.');
    }

    public function edit(TypePieceEntreprise $type): View
    {
        return view('referentiel.types-piece.edit', ['type' => $type]);
    }

    public function update(Request $request, TypePieceEntreprise $type): RedirectResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:255']]);

        $type->update([
            'type' => $data['type'],
            'periode_associee' => $request->boolean('periode_associee'),
        ]);

        return redirect()
            ->route('referentiel.types-piece.index')
            ->with('status', 'Type de document mis à jour.');
    }

    public function destroy(TypePieceEntreprise $type): RedirectResponse
    {
        $type->delete();

        return redirect()
            ->route('referentiel.types-piece.index')
            ->with('status', 'Type de document supprimé.');
    }
}
