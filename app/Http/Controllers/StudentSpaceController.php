<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\BulletinEleve;
use App\Models\Note;
use App\Models\SnBulletin;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage du volet "libre-service élève" de `User.php` (CodeIgniter) :
 * `my_evaluations()`, `my_planning()`, `bulletins()`/`bulletins_disponibles()`.
 *
 * Simplifications assumées : uniquement la consultation (lecture seule) des
 * notes publiées, du planning et des bulletins publiés de l'élève connecté
 * — pas les filtres avancés (UE/matière/semestre en cascade) ni la partie
 * "mes informations" (édition du dossier, upload de documents, choix
 * d'options, justificatifs d'absence...), qui reste un chantier séparé.
 */
class StudentSpaceController extends Controller
{
    public function myEvaluations(): View
    {
        $eleve = Auth::guard('eleve')->user()->eleve;

        $notes = Note::with(['evaluation.unite', 'evaluation.matiere', 'evaluation.typeEvaluation.type'])
            ->where('id_eleve', $eleve->id_eleve)
            ->where('publier_eleve', true)
            ->orderByDesc('date_saisie')
            ->get()
            ->groupBy(fn (Note $n) => $n->evaluation?->unite?->nom_unite_enseignement ?? 'Autre');

        return view('espace-eleve.evaluations', ['notesParUe' => $notes]);
    }

    public function myPlanning(): View
    {
        $eleve = Auth::guard('eleve')->user()->eleve;

        $creneaux = ActiviteIntervenant::with(['intervenant', 'cours'])
            ->where('id_classe', $eleve->id_classe)
            ->where('date_debut', '>=', now()->subDays(7))
            ->orderBy('date_debut')
            ->get();

        return view('espace-eleve.planning', ['creneaux' => $creneaux]);
    }

    public function myBulletins(): View
    {
        $eleve = Auth::guard('eleve')->user()->eleve;

        $bulletinsAdmin = BulletinEleve::where('id_eleve', $eleve->id_eleve)
            ->where('est_publie', '1')
            ->orderByDesc('date_create')
            ->get();

        $bulletinsPdf = SnBulletin::where('id_eleve', $eleve->id_eleve)
            ->where('active', true)
            ->orderByDesc('date_insert')
            ->get();

        return view('espace-eleve.bulletins', compact('bulletinsAdmin', 'bulletinsPdf'));
    }

    public function downloadBulletin(SnBulletin $bulletin): Response
    {
        abort_unless($bulletin->id_eleve === Auth::guard('eleve')->user()->id_eleve, 403);

        return response($bulletin->pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="bulletin-'.$bulletin->id_bulletin.'.pdf"',
        ]);
    }
}
