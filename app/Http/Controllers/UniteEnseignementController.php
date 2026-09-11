<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use App\Models\UniteEnseignement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage de la gestion des Unités d'Enseignement dans `Referentiel.php`
 * (get_unite / add_unite / supp_unite / update_unite / valide_update_unite).
 */
class UniteEnseignementController extends Controller
{
    public function index(Request $request): View
    {
        $unites = UniteEnseignement::query()
            ->with(['niveau', 'annees'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom_unite_enseignement', 'like', "%{$terme}%")
                    ->orWhere('code_unite', 'like', "%{$terme}%");
            })
            ->orderBy('nom_unite_enseignement')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.unites.index', ['unites' => $unites]);
    }

    public function create(): View
    {
        return view('referentiel.unites.create', ['niveaux' => Niveau::orderBy('nom_niveau')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code_unite' => ['required', 'string', 'max:50'],
            'nom_unite_enseignement' => ['required', 'string', 'max:255'],
            'id_niveau' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'couleur' => ['nullable', 'string', 'max:10'],
            'annees' => ['array'],
            'annees.*' => ['integer'],
        ]);

        $existe = UniteEnseignement::where('code_unite', $data['code_unite'])
            ->orWhere('nom_unite_enseignement', $data['nom_unite_enseignement'])
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors(['code_unite' => "Cette unité d'enseignement existe déjà."]);
        }

        $unite = DB::transaction(function () use ($data) {
            $unite = UniteEnseignement::create([
                'code_unite' => $data['code_unite'],
                'nom_unite_enseignement' => $data['nom_unite_enseignement'],
                'id_niveau' => $data['id_niveau'],
                'couleur' => $data['couleur'] ?? '#cccccc',
            ]);

            $unite->annees()->createMany(
                collect($data['annees'] ?? [])->map(fn ($annee) => ['annee' => $annee])->all()
            );

            return $unite;
        });

        return redirect()
            ->route('referentiel.unites.edit', $unite)
            ->with('status', "Unité d'enseignement « {$unite->nom_unite_enseignement} » créée avec succès.");
    }

    public function edit(UniteEnseignement $unite): View
    {
        $unite->load('annees');

        return view('referentiel.unites.edit', [
            'unite' => $unite,
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
        ]);
    }

    public function update(Request $request, UniteEnseignement $unite): RedirectResponse
    {
        $data = $request->validate([
            'code_unite' => ['required', 'string', 'max:50'],
            'nom_unite_enseignement' => ['required', 'string', 'max:255'],
            'id_niveau' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'couleur' => ['nullable', 'string', 'max:10'],
            'annees' => ['array'],
            'annees.*' => ['integer'],
        ]);

        DB::transaction(function () use ($data, $unite) {
            $unite->update([
                'code_unite' => $data['code_unite'],
                'nom_unite_enseignement' => $data['nom_unite_enseignement'],
                'id_niveau' => $data['id_niveau'],
                'couleur' => $data['couleur'] ?? $unite->couleur,
            ]);

            $unite->annees()->delete();
            $unite->annees()->createMany(
                collect($data['annees'] ?? [])->map(fn ($annee) => ['annee' => $annee])->all()
            );
        });

        return redirect()
            ->route('referentiel.unites.edit', $unite)
            ->with('status', "Unité d'enseignement « {$unite->nom_unite_enseignement} » mise à jour.");
    }

    public function destroy(UniteEnseignement $unite): RedirectResponse
    {
        $unite->delete();

        return redirect()
            ->route('referentiel.unites.index')
            ->with('status', "Unité d'enseignement supprimée.");
    }
}
