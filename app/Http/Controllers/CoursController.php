<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Niveau;
use App\Models\UniteEnseignement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage de la gestion des cours dans `Referentiel.php`
 * (get_cours / add_cours / supp_cours / update_cours / valide_update_cours).
 *
 * NON couvert : `check_cours_in_referentiel()` (empêche la suppression d'un
 * cours déjà utilisé dans un référentiel de niveau/classe) — dépend des
 * tables `referentiel_niveau` / `referentiel_classe`, dont la gestion
 * (association cours ↔ niveau/classe pour construire un référentiel de
 * formation) est un sous-module encore plus vaste, à traiter séparément.
 * Ici la suppression est directe (`onDelete cascade` sur les compétences).
 */
class CoursController extends Controller
{
    public function index(Request $request): View
    {
        $cours = Cours::query()
            ->with(['unite', 'annees'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom_cours', 'like', "%{$terme}%")
                    ->orWhere('code_cours', 'like', "%{$terme}%");
            })
            ->orderBy('nom_cours')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.cours.index', ['cours' => $cours]);
    }

    public function create(): View
    {
        return view('referentiel.cours.create', ['unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code_cours' => ['required', 'string', 'max:100'],
            'nom_cours' => ['required', 'string'],
            // L'unite d'enseignement est un decoupage du superieur, ecarte du
            // K-12 : la table est vide, et une regle `required|exists` rendait
            // la creation d'une matiere impossible. Elle reste acceptee pour
            // les installations qui s'en servent encore.
            'id_unite_enseignement' => ['nullable', 'integer'],
            'annees' => ['array'],
            'annees.*' => ['integer'],
        ]);

        $cours = DB::transaction(function () use ($data) {
            $cours = Cours::create([
                'code_cours' => $data['code_cours'],
                'nom_cours' => $data['nom_cours'],
                'id_unite_enseignement' => $data['id_unite_enseignement'] ?? 0,
            ]);

            foreach ($data['annees'] ?? [] as $annee) {
                $cours->annees()->create([
                    'id_unite_enseignement' => $data['id_unite_enseignement'] ?? 0,
                    'annee' => $annee,
                ]);
            }

            return $cours;
        });

        return redirect()
            ->route('referentiel.cours.edit', $cours)
            ->with('status', "Cours « {$cours->nom_cours} » créé avec succès.");
    }

    public function edit(Cours $cours): View
    {
        $cours->load(['annees', 'competences']);

        return view('referentiel.cours.edit', [
            'cours' => $cours,
            'unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get(),
            // Les niveaux qui l'enseignent, avec leur coefficient : c'est la
            // que la matiere prend son sens en K-12, pas dans une UE.
            'niveaux' => Niveau::with(['formation', 'matieres'])
                ->whereHas('matieres', fn ($q) => $q->where('amos_cours.id_cours', $cours->id_cours))
                ->get()
                ->sortBy(fn ($n) => sprintf('%02d|%s', $n->formation?->priorite ?? 99, $n->nom_niveau)),
        ]);
    }

    public function update(Request $request, Cours $cours): RedirectResponse
    {
        $data = $request->validate([
            'code_cours' => ['required', 'string', 'max:100'],
            'nom_cours' => ['required', 'string'],
            // L'unite d'enseignement est un decoupage du superieur, ecarte du
            // K-12 : la table est vide, et une regle `required|exists` rendait
            // la creation d'une matiere impossible. Elle reste acceptee pour
            // les installations qui s'en servent encore.
            'id_unite_enseignement' => ['nullable', 'integer'],
            'annees' => ['array'],
            'annees.*' => ['integer'],
        ]);

        DB::transaction(function () use ($data, $cours) {
            $cours->update([
                'code_cours' => $data['code_cours'],
                'nom_cours' => $data['nom_cours'],
                'id_unite_enseignement' => $data['id_unite_enseignement'] ?? 0,
            ]);

            $cours->annees()->delete();
            foreach ($data['annees'] ?? [] as $annee) {
                $cours->annees()->create([
                    'id_unite_enseignement' => $data['id_unite_enseignement'] ?? 0,
                    'annee' => $annee,
                ]);
            }
        });

        return redirect()
            ->route('referentiel.cours.edit', $cours)
            ->with('status', "Cours « {$cours->nom_cours} » mis à jour.");
    }

    public function destroy(Cours $cours): RedirectResponse
    {
        $cours->delete();

        return redirect()
            ->route('referentiel.cours.index')
            ->with('status', 'Cours supprimé.');
    }
}
