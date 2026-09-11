<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Etablissement;
use App\Models\Recapitulatif;
use App\Models\TypeCours;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Recapitulatif.php` (CodeIgniter) — espace intervenant, saisie
 * du récapitulatif d'heures effectuées.
 *
 * Simplifications assumées :
 * - Le calendrier FullCalendar interactif du legacy est remplacé par un
 *   formulaire de saisie multi-lignes classique + une liste triable.
 * - `get_event()` (autocomplete "recherche de cours" côté JS) n'est pas
 *   porté : le formulaire propose un simple <select> des cours existants.
 */
class RecapitulatifController extends Controller
{
    public function index(): View
    {
        return view('intervenant.recapitulatif.index', [
            'classes' => Classe::orderBy('classe')->get(),
            'coursListe' => Cours::orderBy('nom_cours')->get(),
            'typesCours' => TypeCours::orderBy('type_cours')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date_recap' => ['required', 'array'],
            'date_recap.*' => ['date'],
            'classe_recap' => ['required', 'array'],
            'classe_recap.*' => ['integer', 'exists:amos_classe,id_classe'],
            'type_cour_recap' => ['required', 'array'],
            'type_cour_recap.*' => ['integer', 'exists:amos_type_cours,id_type_cours'],
            'time_debut_recap' => ['required', 'array'],
            'time_debut_recap.*' => ['date_format:H:i'],
            'time_fin_recap' => ['required', 'array'],
            'time_fin_recap.*' => ['date_format:H:i'],
            'intitule_recap_id' => ['required', 'array'],
            'intitule_recap_id.*' => ['integer', 'exists:amos_cours,id_cours'],
            'volumeh' => ['required', 'array'],
            'volumeh.*' => ['date_format:H:i'],
            'etablissementH' => ['required', 'array'],
            'etablissementH.*' => ['integer', 'exists:amos_etablissement,id_etablissement'],
        ]);

        foreach ($data['date_recap'] as $i => $date) {
            Recapitulatif::create([
                'date_recapitulatif' => $date,
                'id_classe' => $data['classe_recap'][$i],
                'id_type_cours' => $data['type_cour_recap'][$i],
                'hdebut' => $data['time_debut_recap'][$i],
                'hfin' => $data['time_fin_recap'][$i],
                'id_cours' => $data['intitule_recap_id'][$i],
                'volume_horaire' => $data['volumeh'][$i],
                'id_etablissement' => $data['etablissementH'][$i],
            ]);
        }

        return redirect()
            ->route('recapitulatif.resume')
            ->with('status', 'Récapitulatif enregistré avec succès.');
    }

    public function resume(Request $request): View
    {
        $etablissementsDisponibles = Etablissement::whereIn(
            'id_etablissement',
            Recapitulatif::query()->distinct()->pluck('id_etablissement')
        )->orderBy('nom_etablissement')->get();

        $lignes = Recapitulatif::with(['classe', 'typeCours', 'cours', 'etablissement'])
            ->when($request->filled('id_etablissement'), fn ($q) => $q->where('id_etablissement', $request->integer('id_etablissement')))
            ->orderByDesc('date_recapitulatif')
            ->get();

        return view('intervenant.recapitulatif.resume', [
            'lignes' => $lignes,
            'etablissementsDisponibles' => $etablissementsDisponibles,
            'etablissementSelectionne' => $request->integer('id_etablissement'),
        ]);
    }
}
