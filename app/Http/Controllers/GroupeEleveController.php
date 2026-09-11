<?php

namespace App\Http\Controllers;

use App\Models\GroupeEleve;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de la gestion des groupes d'élèves dans `Referentiel.php`
 * (get_groupes / add_groupe / supp_groupe / update_groupe / valide_update_groupe).
 */
class GroupeEleveController extends Controller
{
    public function index(Request $request): View
    {
        $groupes = GroupeEleve::query()
            ->when($request->filled('recherche'), fn ($q) => $q->where('nom_groupe', 'like', '%'.$request->string('recherche').'%'))
            ->orderBy('nom_groupe')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.groupes.index', ['groupes' => $groupes]);
    }

    public function create(): View
    {
        return view('referentiel.groupes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nom_groupe' => ['required', 'string', 'max:150']]);

        $groupe = GroupeEleve::create([
            'nom_groupe' => $data['nom_groupe'],
            'date_creation' => now(),
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('referentiel.groupes.index')
            ->with('status', "Groupe « {$groupe->nom_groupe} » créé avec succès.");
    }

    public function edit(GroupeEleve $groupe): View
    {
        return view('referentiel.groupes.edit', ['groupe' => $groupe]);
    }

    public function update(Request $request, GroupeEleve $groupe): RedirectResponse
    {
        $data = $request->validate(['nom_groupe' => ['required', 'string', 'max:150']]);

        $groupe->update([
            'nom_groupe' => $data['nom_groupe'],
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('referentiel.groupes.index')
            ->with('status', "Groupe « {$groupe->nom_groupe} » mis à jour.");
    }

    public function destroy(GroupeEleve $groupe): RedirectResponse
    {
        $groupe->delete();

        return redirect()
            ->route('referentiel.groupes.index')
            ->with('status', 'Groupe supprimé.');
    }
}
