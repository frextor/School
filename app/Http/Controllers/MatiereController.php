<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\UniteEnseignement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de la gestion des matières dans `Referentiel.php`
 * (get_matiere / add_matiere / supp_matiere / update_matiere / valide_update_matiere).
 *
 * NON couvert : `set_trace_admin()` (journalisation), dépend du module Crm/journal.
 */
class MatiereController extends Controller
{
    public function index(Request $request): View
    {
        $matieres = Matiere::query()
            ->with('unite')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom_matiere', 'like', "%{$terme}%")
                    ->orWhere('code_matiere', 'like', "%{$terme}%");
            })
            ->orderBy('nom_matiere')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.matieres.index', ['matieres' => $matieres]);
    }

    public function create(): View
    {
        return view('referentiel.matieres.create', ['unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code_matiere' => ['required', 'string', 'max:5'],
            'nom_matiere' => ['required', 'string'],
            'id_unite_enseignement' => ['required', 'integer', 'exists:amos_unite_enseignement,id_unite_enseignement'],
        ]);

        $matiere = Matiere::create($data);

        return redirect()
            ->route('referentiel.matieres.index')
            ->with('status', "Matière « {$matiere->nom_matiere} » créée avec succès.");
    }

    public function edit(Matiere $matiere): View
    {
        return view('referentiel.matieres.edit', [
            'matiere' => $matiere,
            'unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get(),
        ]);
    }

    public function update(Request $request, Matiere $matiere): RedirectResponse
    {
        $data = $request->validate([
            'code_matiere' => ['required', 'string', 'max:5'],
            'nom_matiere' => ['required', 'string'],
            'id_unite_enseignement' => ['required', 'integer', 'exists:amos_unite_enseignement,id_unite_enseignement'],
        ]);

        $matiere->update($data);

        return redirect()
            ->route('referentiel.matieres.index')
            ->with('status', "Matière « {$matiere->nom_matiere} » mise à jour.");
    }

    public function destroy(Matiere $matiere): RedirectResponse
    {
        $matiere->delete();

        return redirect()
            ->route('referentiel.matieres.index')
            ->with('status', 'Matière supprimée.');
    }
}
