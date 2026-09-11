<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Niveau;
use App\Models\PeriodeFormation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage de la gestion des périodes de formation dans `Referentiel.php`
 * (get_config_periodes_formation / add_config_periode_formation /
 * update_config_periode_formation / supp_config_periode_formation +
 * la gestion des périodes trimestrielles associées).
 */
class PeriodeFormationController extends Controller
{
    public function index(): View
    {
        $periodes = PeriodeFormation::with(['niveaux', 'classes'])
            ->orderByDesc('annee_scolaire')
            ->paginate(25);

        return view('referentiel.periodes-formation.index', ['periodes' => $periodes]);
    }

    public function create(): View
    {
        return view('referentiel.periodes-formation.create', [
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request);

        $periode = DB::transaction(fn () => $this->enregistrer(PeriodeFormation::make(), $data));

        return redirect()
            ->route('referentiel.periodes-formation.index')
            ->with('status', "Période de formation « {$periode->periode} » créée avec succès.");
    }

    public function edit(PeriodeFormation $periodesFormation): View
    {
        $periodesFormation->load(['niveaux', 'classes', 'periodesTrimestrielles']);

        return view('referentiel.periodes-formation.edit', [
            'periode' => $periodesFormation,
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(),
        ]);
    }

    public function update(Request $request, PeriodeFormation $periodesFormation): RedirectResponse
    {
        $data = $this->validerDonnees($request);

        DB::transaction(function () use ($periodesFormation, $data) {
            $periodesFormation->periodesTrimestrielles()->delete();
            $this->enregistrer($periodesFormation, $data);
        });

        return redirect()
            ->route('referentiel.periodes-formation.index')
            ->with('status', "Période de formation « {$periodesFormation->periode} » mise à jour.");
    }

    public function destroy(PeriodeFormation $periodesFormation): RedirectResponse
    {
        DB::transaction(function () use ($periodesFormation) {
            $periodesFormation->niveaux()->detach();
            $periodesFormation->classes()->detach();
            $periodesFormation->periodesTrimestrielles()->delete();
            $periodesFormation->delete();
        });

        return redirect()
            ->route('referentiel.periodes-formation.index')
            ->with('status', 'Période de formation supprimée.');
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'annee_scolaire' => ['required', 'string', 'max:100'],
            'periode' => ['required', 'string', 'max:255'],
            'nb_heure_annuel' => ['nullable', 'numeric'],
            'diplome_rncp' => ['nullable', 'string', 'max:255'],
            'code_diplome' => ['nullable', 'string', 'max:255'],
            'id_niveau' => ['array'],
            'id_niveau.*' => ['integer', 'exists:amos_niveaux,id_niveau'],
            'id_classe' => ['array'],
            'id_classe.*' => ['integer', 'exists:amos_classe,id_classe'],
            'periode_trimestrielle' => ['array'],
            'periode_trimestrielle.*' => ['string', 'max:255'],
            'nb_heure_trimestriel' => ['array'],
            'nb_heure_trimestriel.*' => ['nullable', 'numeric'],
        ]);
    }

    private function enregistrer(PeriodeFormation $periode, array $data): PeriodeFormation
    {
        $periode->fill([
            'annee_scolaire' => $data['annee_scolaire'],
            'periode' => $data['periode'],
            'nb_heure_annuel' => $data['nb_heure_annuel'] ?? 0,
            'diplome_rncp' => $data['diplome_rncp'] ?? '',
            'code_diplome' => $data['code_diplome'] ?? '',
        ])->save();

        $periode->niveaux()->sync($data['id_niveau'] ?? []);
        $periode->classes()->sync($data['id_classe'] ?? []);

        foreach ($data['periode_trimestrielle'] ?? [] as $k => $libelle) {
            $periode->periodesTrimestrielles()->create([
                'periode' => $libelle,
                'nb_heure' => $data['nb_heure_trimestriel'][$k] ?? 0,
            ]);
        }

        return $periode;
    }
}
