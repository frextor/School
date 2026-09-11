<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de la gestion des classes dans `Referentiel.php`
 * (get_classe / add_classe / supp_classe / update_classe / valide_update_classe).
 */
class ClasseController extends Controller
{
    public function index(Request $request): View
    {
        $classes = Classe::query()
            ->with(['niveau', 'etablissement'])
            ->when($request->filled('recherche'), fn ($q) => $q->where('classe', 'like', '%'.$request->string('recherche').'%'))
            ->orderBy('classe')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.classes.index', ['classes' => $classes]);
    }

    public function create(): View
    {
        return view('referentiel.classes.create', [
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'classe' => ['required', 'string'],
            'id_niveau' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'couleur' => ['nullable', 'string', 'max:10'],
        ]);

        $classe = Classe::create([
            'classe' => $data['classe'],
            'code_classe' => '',
            'id_niveau' => $data['id_niveau'],
            'id_etablissement' => $data['id_etablissement'],
            'couleur' => $data['couleur'] ?? '#cccccc',
        ]);

        return redirect()
            ->route('referentiel.classes.edit', $classe)
            ->with('status', "Classe « {$classe->classe} » créée avec succès.");
    }

    public function edit(Classe $classe): View
    {
        return view('referentiel.classes.edit', [
            'classe' => $classe,
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function update(Request $request, Classe $classe): RedirectResponse
    {
        $data = $request->validate([
            'classe' => ['required', 'string'],
            'id_niveau' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'couleur' => ['nullable', 'string', 'max:10'],
        ]);

        $classe->update($data);

        return redirect()
            ->route('referentiel.classes.edit', $classe)
            ->with('status', "Classe « {$classe->classe} » mise à jour.");
    }

    public function destroy(Classe $classe): RedirectResponse
    {
        $classe->delete();

        return redirect()
            ->route('referentiel.classes.index')
            ->with('status', 'Classe supprimée.');
    }
}
