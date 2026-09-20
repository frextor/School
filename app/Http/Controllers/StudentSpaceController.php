<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\BulletinEleve;
use App\Models\Note;
use App\Models\SnBulletin;
use App\Support\Calendrier;
use Illuminate\Http\Request;
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
    /**
     * Accueil de l'espace : le prochain cours, les dernières notes et le
     * dernier bulletin. L'écran ne portait qu'un bouton de déconnexion.
     */
    public function home(): View
    {
        $eleve = Auth::guard('eleve')->user()->eleve;
        $debutSemaine = Calendrier::debutSemaine(null);

        return view('eleve.dashboard', [
            'eleve' => $eleve,
            'prochainCours' => ActiviteIntervenant::with(['cours', 'intervenant', 'salle'])
                ->where('id_classe', $eleve->id_classe)
                ->where('date_debut', '>=', now())
                ->orderBy('date_debut')
                ->first(),
            'coursSemaine' => ActiviteIntervenant::where('id_classe', $eleve->id_classe)
                ->whereBetween('date_debut', [$debutSemaine, $debutSemaine->copy()->addDays(6)->endOfDay()])
                ->count(),
            'dernieresNotes' => Note::with('evaluation.matiere')
                ->where('id_eleve', $eleve->id_eleve)
                ->where('publier_eleve', true)
                ->orderByDesc('date_saisie')
                ->limit(4)
                ->get(),
            'dernierBulletin' => SnBulletin::where('id_eleve', $eleve->id_eleve)
                ->where('active', true)
                ->orderByDesc('date_insert')
                ->first(),
        ]);
    }

    public function myEvaluations(): View
    {
        $eleve = Auth::guard('eleve')->user()->eleve;

        // Regroupement par **matière** : l'unité d'enseignement est un
        // découpage du supérieur, sans objet en K-12 où l'élève raisonne
        // par matière (toutes les notes tombaient dans « Autre »).
        $notes = Note::with(['evaluation.matiere', 'evaluation.typeEvaluation.type'])
            ->where('id_eleve', $eleve->id_eleve)
            ->where('publier_eleve', true)
            ->orderByDesc('date_saisie')
            ->get()
            ->groupBy(fn (Note $n) => $n->evaluation?->matiere?->nom_cours ?? 'Autre');

        return view('espace-eleve.evaluations', ['notesParUe' => $notes]);
    }

    /** Emploi du temps de la semaine, navigable (`?semaine=AAAA-MM-JJ`). */
    public function myPlanning(Request $request): View
    {
        $eleve = Auth::guard('eleve')->user()->eleve;
        $debutSemaine = Calendrier::debutSemaine($request->string('semaine')->toString());

        $creneaux = ActiviteIntervenant::with(['intervenant', 'cours', 'salle'])
            ->where('id_classe', $eleve->id_classe)
            ->whereBetween('date_debut', [$debutSemaine, $debutSemaine->copy()->addDays(6)->endOfDay()])
            ->orderBy('date_debut')
            ->get();

        return view('espace-eleve.planning', [
            'debutSemaine' => $debutSemaine,
            'semaine' => Calendrier::semaine($creneaux->map(fn (ActiviteIntervenant $c) => [
                'debut' => $c->date_debut,
                'fin' => $c->date_fin,
                'titre' => $c->cours?->nom_cours ?: 'Cours',
                'meta' => collect([
                    trim(($c->intervenant?->nom ?? '').' '.($c->intervenant?->prenom ?? '')) ?: null,
                    $c->salle?->nom_salle ? 'Salle '.$c->salle->nom_salle : null,
                ])->filter()->implode(' · '),
                'couleur' => Calendrier::couleurMatiere($c->cours?->nom_cours),
            ]), $debutSemaine),
        ]);
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
