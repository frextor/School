<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de la gestion des niveaux dans `Referentiel.php`
 * (get_niveau / add_niveau / supp_niveau / update_niveau / valide_update_niveau).
 *
 * NON couvert : `prepare_conventions()` / `delete_conventions_by_niveau()`
 * (génération automatique de conventions de stage entreprise à la création
 * d'un niveau) — dépend du module Entreprises, migré séparément.
 */
class NiveauController extends Controller
{
    public function index(Request $request): View
    {
        $niveaux = Niveau::query()
            ->with('etablissements', 'formation')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom_niveau', 'like', "%{$terme}%")
                    ->orWhere('code_niveau', 'like', "%{$terme}%");
            })
            ->orderBy('nom_niveau')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.niveaux.index', ['niveaux' => $niveaux]);
    }

    public function create(): View
    {
        return view('referentiel.niveaux.create', ['formations' => Formation::orderBy('niveau')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code_niveau' => ['required', 'string', 'max:50', 'unique:amos_niveaux,code_niveau'],
            'nom_niveau' => ['required', 'string', 'max:64'],
            'id_formation' => ['required', 'integer', 'exists:amos_formations,id_formation'],
        ]);

        $niveau = Niveau::create([
            'code_niveau' => ucfirst($data['code_niveau']),
            'nom_niveau' => ucfirst($data['nom_niveau']),
            'id_formation' => $data['id_formation'],
            'id_niveau_future' => 0,
            'deuxieme_langue' => 0,
        ]);

        return redirect()
            ->route('referentiel.niveaux.edit', $niveau)
            ->with('status', "Niveau « {$niveau->nom_niveau} » créé avec succès.");
    }

    public function edit(Niveau $niveau): View
    {
        return view('referentiel.niveaux.edit', [
            'niveau' => $niveau,
            'formations' => Formation::orderBy('niveau')->get(),
            'niveaux' => Niveau::where('id_niveau', '!=', $niveau->id_niveau)->orderBy('nom_niveau')->get(),
        ]);
    }

    public function update(Request $request, Niveau $niveau): RedirectResponse
    {
        $data = $request->validate([
            'code_niveau' => ['required', 'string', 'max:50', 'unique:amos_niveaux,code_niveau,'.$niveau->id_niveau.',id_niveau'],
            'nom_niveau' => ['required', 'string', 'max:64'],
            'id_formation' => ['required', 'integer', 'exists:amos_formations,id_formation'],
            'id_niveau_future' => ['nullable', 'integer', 'exists:amos_niveaux,id_niveau'],
        ]);

        $niveau->update([
            'code_niveau' => $data['code_niveau'],
            'nom_niveau' => ucfirst($data['nom_niveau']),
            'id_formation' => $data['id_formation'],
            'id_niveau_future' => $data['id_niveau_future'] ?? 0,
        ]);

        return redirect()
            ->route('referentiel.niveaux.edit', $niveau)
            ->with('status', "Niveau « {$niveau->nom_niveau} » mis à jour.");
    }

    public function destroy(Niveau $niveau): RedirectResponse
    {
        $niveau->delete();

        return redirect()
            ->route('referentiel.niveaux.index')
            ->with('status', 'Niveau supprimé.');
    }
}
