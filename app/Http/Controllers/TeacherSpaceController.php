<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Eleve;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage du volet "libre-service intervenant" de `User_intervenant.php`
 * (CodeIgniter) : `planning()` et `trombinoscope()`/`get_eleves_classe()`.
 *
 * Simplifications assumées : liste des classes déduite des créneaux
 * planifiés de l'intervenant (plutôt que la relation établissement↔niveau
 * complète du legacy) ; pas de filtre par groupe ni de gestion des
 * absences/déplacements (voir `NoteController`/`RecapitulatifController`
 * pour le reste de l'espace intervenant déjà migré).
 */
class TeacherSpaceController extends Controller
{
    public function myPlanning(): View
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;

        $creneaux = ActiviteIntervenant::with(['etablissement', 'cours', 'classe'])
            ->where('id_intervenant', $intervenant->id_intervenant)
            ->where('date_debut', '>=', now()->subDays(7))
            ->orderBy('date_debut')
            ->get();

        return view('espace-intervenant.planning', ['creneaux' => $creneaux]);
    }

    public function myClasses(): View
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;

        $idsClasses = ActiviteIntervenant::where('id_intervenant', $intervenant->id_intervenant)
            ->whereNotNull('id_classe')
            ->where('id_classe', '!=', '')
            ->distinct()
            ->pluck('id_classe');

        $classes = Classe::whereIn('id_classe', $idsClasses)->with('niveau')->get();

        return view('espace-intervenant.classes', ['classes' => $classes]);
    }

    /** Portage simplifié de `get_eleves_classe()` — trombinoscope/liste des élèves d'une classe. */
    public function classRoster(Classe $classe): View
    {
        $eleves = Eleve::where('id_classe', $classe->id_classe)
            ->with('contact')
            ->actifs()
            ->get();

        return view('espace-intervenant.roster', ['classe' => $classe, 'eleves' => $eleves]);
    }
}
