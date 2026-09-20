<?php

namespace App\Http\Controllers;

use App\Models\TypeEvaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Portage de `add_type_evaluation()` / `update_type_evaluation()` /
 * `delete_type_evaluation()` — le dictionnaire des types d'évaluation
 * (`amos_type_evaluation`).
 *
 * Ce dictionnaire ne porte que le **nom** du type. Le coefficient, lui, est
 * défini par classe et par période dans `amos_sn_type_evaluation` (écran
 * Évaluations) : un contrôle continu peut peser 1 en 6AEP et 2 en 2BAC. La
 * page affiche donc les coefficients constatés, sans les rendre modifiables
 * ici, et le dit explicitement — c'est la première chose que l'on vient y
 * chercher.
 */
class TypeEvaluationController extends Controller
{
    public function index(): View
    {
        $types = TypeEvaluation::orderBy('type')->paginate(25);
        $ids = collect($types->items())->pluck('id_type');

        return view('types-evaluation.index', [
            'types' => $types,
            'usages' => $this->usages($ids),
            // Types attendus dans une école marocaine : proposés en un clic
            // tant qu'ils manquent, plutôt que ressaisis à la main.
            'suggestions' => collect(TypeEvaluation::TYPES_MAROC)
                ->reject(fn ($nom) => TypeEvaluation::where('type', $nom)->exists())
                ->values(),
        ]);
    }

    /**
     * Ce qui s'appuie sur chaque type : paramétrages de coefficient,
     * évaluations programmées et notes saisies. Trois agrégats groupés plutôt
     * qu'un comptage par ligne du tableau.
     *
     * @return \Illuminate\Support\Collection<int, array>
     */
    private function usages(\Illuminate\Support\Collection $ids): \Illuminate\Support\Collection
    {
        if ($ids->isEmpty()) {
            return collect();
        }

        $parametrages = DB::table('amos_sn_type_evaluation')
            ->whereIn('id_type', $ids)
            ->selectRaw('id_type, count(*) as total, min(coef) as coef_min, max(coef) as coef_max')
            ->groupBy('id_type')
            ->get()
            ->keyBy('id_type');

        $evaluations = DB::table('amos_sn_evaluations_existantes as e')
            ->join('amos_sn_type_evaluation as t', 't.id_type_evaluation', '=', 'e.id_type_evaluation')
            ->whereIn('t.id_type', $ids)
            ->selectRaw('t.id_type, count(*) as total')
            ->groupBy('t.id_type')
            ->pluck('total', 'id_type');

        $notes = DB::table('amos_sn_base_notes as n')
            ->join('amos_sn_evaluations_existantes as e', 'e.id_evaluation', '=', 'n.id_evaluation')
            ->join('amos_sn_type_evaluation as t', 't.id_type_evaluation', '=', 'e.id_type_evaluation')
            ->whereIn('t.id_type', $ids)
            ->selectRaw('t.id_type, count(*) as total')
            ->groupBy('t.id_type')
            ->pluck('total', 'id_type');

        return $ids->mapWithKeys(function ($id) use ($parametrages, $evaluations, $notes) {
            $parametrage = $parametrages->get($id);

            return [$id => [
                'parametrages' => (int) ($parametrage->total ?? 0),
                'coef_min' => $parametrage->coef_min ?? null,
                'coef_max' => $parametrage->coef_max ?? null,
                'evaluations' => (int) ($evaluations[$id] ?? 0),
                'notes' => (int) ($notes[$id] ?? 0),
            ]];
        });
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // La table n'a pas d'index unique : sans cette règle, deux
            // « Contrôle continu » coexistent et l'on ne sait plus lequel
            // choisir dans les évaluations.
            'type' => ['required', 'string', 'max:50', Rule::unique('amos_type_evaluation', 'type')],
        ], [], ['type' => "nom du type d'évaluation"]);

        TypeEvaluation::create($data);

        return redirect()->route('types-evaluation.index')
            ->with('status', "Type d'évaluation « {$data['type']} » créé.");
    }

    public function update(Request $request, TypeEvaluation $type): RedirectResponse
    {
        $data = $request->validate([
            'type' => [
                'required', 'string', 'max:50',
                Rule::unique('amos_type_evaluation', 'type')->ignore($type->id_type, 'id_type'),
            ],
        ], [], ['type' => "nom du type d'évaluation"]);

        $ancien = $type->type;
        $type->update($data);

        return redirect()->route('types-evaluation.index')
            ->with('status', "« {$ancien} » renommé en « {$data['type']} ».");
    }

    public function destroy(TypeEvaluation $type): RedirectResponse
    {
        $usage = $this->usages(collect([$type->id_type]))->get($type->id_type);

        // Supprimer un type utilisé laisserait des évaluations et des notes
        // rattachées à un type disparu : le bulletin ne saurait plus les
        // pondérer. On refuse en expliquant ce qui s'y rattache.
        if ($usage && ($usage['parametrages'] || $usage['evaluations'] || $usage['notes'])) {
            $details = collect([
                $usage['parametrages'] ? $usage['parametrages'].' paramétrage(s) de coefficient' : null,
                $usage['evaluations'] ? $usage['evaluations'].' évaluation(s)' : null,
                $usage['notes'] ? $usage['notes'].' note(s)' : null,
            ])->filter()->implode(', ');

            return redirect()->route('types-evaluation.index')
                ->with('error', "« {$type->type} » ne peut pas être supprimé : {$details} s'y rattachent. Renommez-le plutôt.");
        }

        $nom = $type->type;
        $type->delete();

        return redirect()->route('types-evaluation.index')
            ->with('status', "Type d'évaluation « {$nom} » supprimé.");
    }
}
