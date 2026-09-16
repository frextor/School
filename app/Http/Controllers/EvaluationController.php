<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Etablissement;
use App\Models\Evaluation;
use App\Models\GroupeEleve;
use App\Models\SnTypeEvaluation;
use App\Models\UniteEnseignement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage du sous-système "évaluations" de `Notation.php`
 * (create_evaluation / update_evaluation / delete_evaluation, via
 * `notation_library` côté legacy).
 *
 * Simplification assumée : `type_notation` (chaîne libre 2 caractères côté
 * legacy, ex. "20", "CM"...) est conservée telle quelle, la logique de
 * barème n'étant pas documentée dans le controller source.
 */
class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $evaluations = Evaluation::with(['campus', 'unite', 'matiere'])
            ->when($request->filled('id_campus'), fn ($q) => $q->where('id_campus', $request->integer('id_campus')))
            ->when($request->filled('annee'), fn ($q) => $q->where('annee', $request->integer('annee')))
            ->orderByDesc('date_evaluation')
            ->paginate(25)
            ->withQueryString();

        return view('evaluations.index', ['evaluations' => $evaluations]);
    }

    public function create(): View
    {
        return view('evaluations.create', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'typesEvaluation' => SnTypeEvaluation::with('type')->get(),
            // Vrai sélecteur classe / groupe plutôt qu'un identifiant à saisir à la main.
            'classes' => Classe::orderBy('classe')->get(['id_classe', 'classe']),
            'groupes' => GroupeEleve::orderBy('nom_groupe')->get(['id_groupe', 'nom_groupe']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request);
        $data['boolean_facultatif'] = $request->boolean('boolean_facultatif');
        $data['id_evaluation_parent'] = 0;
        $data['id_ue'] = $data['id_ue'] ?? 0;

        $evaluation = Evaluation::create($data);

        return redirect()
            ->route('evaluations.edit', $evaluation)
            ->with('status', "Évaluation « {$evaluation->nom_evaluation} » créée avec succès.");
    }

    public function edit(Evaluation $evaluation): View
    {
        return view('evaluations.edit', [
            'evaluation' => $evaluation,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'typesEvaluation' => SnTypeEvaluation::with('type')->get(),
        ]);
    }

    public function update(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $data = $this->validerDonnees($request);
        $data['id_ue'] = $data['id_ue'] ?? 0;
        $data['boolean_facultatif'] = $request->boolean('boolean_facultatif');

        $evaluation->update($data);

        return redirect()
            ->route('evaluations.edit', $evaluation)
            ->with('status', "Évaluation « {$evaluation->nom_evaluation} » mise à jour.");
    }

    public function destroy(Evaluation $evaluation): RedirectResponse
    {
        $evaluation->notes()->delete();
        $evaluation->delete();

        return redirect()
            ->route('evaluations.index')
            ->with('status', 'Évaluation supprimée.');
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'id_campus' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'annee' => ['required', 'integer'],
            'semestre' => ['required', 'integer'],
            'id_referentiel' => ['required', 'integer'],
            'referentiel' => ['required', 'in:classe,groupe'],
            // Facultative depuis le passage au K-12 : on note par matière, l'unité
            // d'enseignement est un découpage du supérieur. Les contraintes en base
            // ont été levées en phase 5 ; cette règle applicative les exigeait encore,
            // ce qui interdisait toute création d'évaluation depuis l'interface.
            'id_ue' => ['nullable', 'integer', 'exists:amos_unite_enseignement,id_unite_enseignement'],
            'id_matiere' => ['required', 'integer', 'exists:amos_cours,id_cours'],
            'id_type_evaluation' => ['required', 'integer', 'exists:amos_sn_type_evaluation,id_type_evaluation'],
            'nom_evaluation' => ['required', 'string', 'max:50'],
            'date_evaluation' => ['required', 'date'],
            'heure_debut' => ['nullable', 'string'],
            'heure_fin' => ['nullable', 'string'],
            'type_notation' => ['required', 'string', 'max:2'],
            'boolean_facultatif' => ['boolean'],
        ]);
    }
}
