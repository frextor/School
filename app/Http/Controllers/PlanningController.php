<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Etablissement;
use App\Models\Intervenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage CRUD simple de `Planning.php` (CodeIgniter) — créneaux de cours
 * ponctuels, sans détection de conflit de disponibilité ni récurrence
 * (décision prise avec l'utilisateur : chantiers séparés, voir le modèle
 * `ActiviteIntervenant`).
 */
class PlanningController extends Controller
{
    public function index(Request $request): View
    {
        $creneaux = ActiviteIntervenant::with(['intervenant', 'etablissement', 'cours', 'classe'])
            ->when($request->filled('id_intervenant'), fn ($q) => $q->where('id_intervenant', $request->integer('id_intervenant')))
            ->when($request->filled('id_etablissement'), fn ($q) => $q->where('id_etablissement', $request->integer('id_etablissement')))
            ->when($request->filled('annee'), fn ($q) => $q->where('annee', $request->integer('annee')))
            ->orderByDesc('date_debut')
            ->paginate(25)
            ->withQueryString();

        return view('planning.index', ['creneaux' => $creneaux]);
    }

    public function create(): View
    {
        return view('planning.create', [
            'intervenants' => Intervenant::orderBy('nom')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'classes' => Classe::orderBy('classe')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->completerChampsObligatoires($this->validerDonnees($request));

        $creneau = ActiviteIntervenant::create($data);

        return redirect()
            ->route('planning.edit', $creneau)
            ->with('status', 'Créneau créé avec succès.');
    }

    public function edit(ActiviteIntervenant $creneau): View
    {
        return view('planning.edit', [
            'creneau' => $creneau,
            'intervenants' => Intervenant::orderBy('nom')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'classes' => Classe::orderBy('classe')->get(),
        ]);
    }

    public function update(Request $request, ActiviteIntervenant $creneau): RedirectResponse
    {
        $data = $this->completerChampsObligatoires($this->validerDonnees($request));

        $creneau->update($data);

        return redirect()
            ->route('planning.edit', $creneau)
            ->with('status', 'Créneau mis à jour.');
    }

    public function destroy(ActiviteIntervenant $creneau): RedirectResponse
    {
        $creneau->delete();

        return redirect()
            ->route('planning.index')
            ->with('status', 'Créneau supprimé.');
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'id_intervenant' => ['required', 'integer', 'exists:amos_intervenant,id_intervenant'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'id_cours' => ['required', 'integer', 'exists:amos_cours,id_cours'],
            'id_classe' => ['nullable', 'string', 'max:50'],
            'id_salle' => ['nullable', 'string', 'max:50'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
            'annotation' => ['nullable', 'string'],
            'semestre' => ['required', 'integer', 'in:1,2'],
            'annee' => ['required', 'integer'],
        ]);
    }

    /** Valeurs par défaut pour les colonnes NOT NULL sans défaut MySQL non couvertes par le formulaire. */
    private function completerChampsObligatoires(array $data): array
    {
        return [
            ...$data,
            'id_classe' => $data['id_classe'] ?? '',
            'id_salle' => $data['id_salle'] ?? '',
            'id_groupe' => '',
            'groupe' => '',
            'annotation' => $data['annotation'] ?? '',
        ];
    }
}
