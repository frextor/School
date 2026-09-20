<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Eleve;
use App\Support\Calendrier;
use Illuminate\Http\Request;
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
    /** Emploi du temps de la semaine, navigable (`?semaine=AAAA-MM-JJ`). */
    public function myPlanning(Request $request): View
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;
        $debutSemaine = Calendrier::debutSemaine($request->string('semaine')->toString());

        $creneaux = ActiviteIntervenant::with(['etablissement', 'cours', 'classe'])
            ->where('id_intervenant', $intervenant->id_intervenant)
            ->whereBetween('date_debut', [$debutSemaine, $debutSemaine->copy()->addDays(6)->endOfDay()])
            ->orderBy('date_debut')
            ->get();

        return view('espace-intervenant.planning', [
            'debutSemaine' => $debutSemaine,
            'semaine' => Calendrier::semaine($creneaux->map(fn (ActiviteIntervenant $c) => [
                'debut' => $c->date_debut,
                'fin' => $c->date_fin,
                'titre' => $c->cours?->nom_cours ?: 'Cours',
                'meta' => collect([
                    $c->classe?->classe ?: null,
                    $c->id_salle ? 'Salle '.$c->id_salle : null,
                ])->filter()->implode(' · '),
                // Couleur de la classe : l'enseignant reconnaît ses groupes
                // d'un coup d'œil, comme sur l'écran d'administration.
                'couleur' => $c->classe?->couleur ?: '#4f46e5',
            ]), $debutSemaine),
        ]);
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
