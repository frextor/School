<?php

namespace App\Http\Controllers;

use App\Models\AbsenceEleve;
use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Eleve;
use App\Support\Appel;
use App\Support\Calendrier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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
    /**
     * Accueil de l'espace : prochain cours, charge de la semaine et classes
     * suivies. L'écran ne portait qu'un lien et un bouton de déconnexion.
     */
    public function home(): View
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;
        $debutSemaine = Calendrier::debutSemaine(null);

        $creneaux = ActiviteIntervenant::where('id_intervenant', $intervenant->id_intervenant);

        // Les prochaines séances ; à défaut les dernières, pour que la carte
        // ne soit pas vide hors période scolaire.
        $seances = fn (bool $futurs) => (clone $creneaux)->with(['cours', 'classe', 'salle'])
            ->where('date_debut', $futurs ? '>=' : '<', now())
            ->orderBy('date_debut', $futurs ? 'asc' : 'desc')
            ->limit(5)
            ->get();

        $prochainsCours = $seances(true);
        $coursPasses = $prochainsCours->isEmpty();

        if ($coursPasses) {
            $prochainsCours = $seances(false)->sortBy('date_debut')->values();
        }

        return view('intervenant.dashboard', [
            'intervenant' => $intervenant,
            'prochainsCours' => $prochainsCours,
            'coursPasses' => $coursPasses,
            'coursSemaine' => (clone $creneaux)
                ->whereBetween('date_debut', [$debutSemaine, $debutSemaine->copy()->addDays(6)->endOfDay()])
                ->count(),
            'classes' => $this->classesSuivies($intervenant->id_intervenant),
        ]);
    }

    /**
     * Classes de l'enseignant, déduites de ses créneaux planifiés, avec leur
     * effectif — le nombre d'élèves est la première chose qu'on veut savoir.
     */
    private function classesSuivies(int $idIntervenant)
    {
        $ids = ActiviteIntervenant::where('id_intervenant', $idIntervenant)
            ->whereNotNull('id_classe')
            ->where('id_classe', '!=', '')
            ->distinct()
            ->pluck('id_classe');

        return Classe::whereIn('id_classe', $ids)
            ->with('niveau')
            ->withCount('eleves')
            ->orderBy('classe')
            ->get();
    }

    /** Emploi du temps de la semaine, navigable (`?semaine=AAAA-MM-JJ`). */
    public function myPlanning(Request $request): View
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;
        $debutSemaine = Calendrier::debutSemaine($request->string('semaine')->toString());

        $creneaux = ActiviteIntervenant::with(['etablissement', 'cours', 'classe', 'salle'])
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
                    $c->salle?->nom_salle ? 'Salle '.$c->salle->nom_salle : null,
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

        return view('espace-intervenant.classes', [
            'classes' => $this->classesSuivies($intervenant->id_intervenant),
        ]);
    }

    /**
     * Feuille d'appel du jour : les cours de l'enseignant pour la date
     * demandee, avec l'etat de l'appel de chacun.
     *
     * L'appel existait cote administration, ou il fallait choisir a la main
     * la classe, la date et l'heure. L'enseignant, lui, part de son cours.
     */
    public function appelDuJour(Request $request): View
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;
        $date = $this->dateDemandee($request);

        $seances = ActiviteIntervenant::with(['cours', 'classe', 'salle'])
            ->where('id_intervenant', $intervenant->id_intervenant)
            ->whereDate('date_debut', $date)
            ->orderBy('date_debut')
            ->get();

        // État de l'appel par séance. La clé doit combiner l'heure **et** la
        // classe : deux cours du même enseignant peuvent commencer à la même
        // heure pour deux classes différentes, et se partageraient alors le
        // même compte d'absences.
        $classeParEleve = Eleve::whereIn('id_classe', $seances->pluck('id_classe')->filter()->unique())
            ->pluck('id_classe', 'id_eleve');

        $saisies = AbsenceEleve::whereDate('date_absence', $date)
            ->whereIn('id_eleve', $classeParEleve->keys())
            ->get()
            ->groupBy(fn (AbsenceEleve $a) => substr((string) $a->heure_absence, 0, 5)
                .'|'.$classeParEleve[$a->id_eleve]);

        return view('espace-intervenant.appel-jour', [
            'date' => $date,
            'seances' => $seances,
            'saisies' => $saisies,
        ]);
    }

    /** Feuille d'appel d'une seance : la classe, eleve par eleve. */
    public function appel(ActiviteIntervenant $creneau): View
    {
        $this->siensOuRefus($creneau);

        $eleves = Eleve::where('id_classe', $creneau->id_classe)
            ->where('profil', Eleve::PROFIL_ELEVE)
            ->where('visible', true)
            ->with('contact')
            ->get()
            ->sortBy(fn (Eleve $e) => $e->contact?->nom_complet)
            ->values();

        $heure = $creneau->date_debut->format('H:i');

        return view('espace-intervenant.appel', [
            'creneau' => $creneau,
            'eleves' => $eleves,
            'existantes' => AbsenceEleve::whereDate('date_absence', $creneau->date_debut)
                ->where('heure_absence', $heure.':00')
                ->whereIn('id_eleve', $eleves->pluck('id_eleve'))
                ->get()
                ->keyBy('id_eleve'),
        ]);
    }

    /** Enregistre l'appel d'une seance (meme ecriture que cote administration). */
    public function enregistrerAppel(Request $request, ActiviteIntervenant $creneau): RedirectResponse
    {
        $this->siensOuRefus($creneau);

        $data = $request->validate([
            'statuts' => ['required', 'array'],
            'statuts.*' => [Appel::STATUTS],
        ]);

        $compte = Appel::enregistrer(
            $data['statuts'],
            $creneau->date_debut,
            $creneau->date_debut->format('H:i'),
            $creneau->id_cours
        );

        return redirect()
            ->route('espace-intervenant.appel-jour', ['date' => $creneau->date_debut->format('Y-m-d')])
            ->with('status', Appel::message($compte));
    }

    /** Un enseignant ne fait l'appel que de ses propres creneaux. */
    private function siensOuRefus(ActiviteIntervenant $creneau): void
    {
        $intervenant = Auth::guard('intervenant')->user()->intervenant;

        abort_unless($creneau->id_intervenant === $intervenant->id_intervenant, 403);
    }

    /** Date consultee (`?date=AAAA-MM-JJ`), aujourd'hui par defaut. */
    private function dateDemandee(Request $request): Carbon
    {
        if ($request->filled('date')) {
            try {
                return Carbon::parse($request->string('date'))->startOfDay();
            } catch (\Exception) {
                // Date illisible : on retombe sur aujourd'hui.
            }
        }

        return Carbon::today();
    }

    /** Portage simplifié de `get_eleves_classe()` — trombinoscope/liste des élèves d'une classe. */
    public function classRoster(Classe $classe): View
    {
        $eleves = Eleve::where('id_classe', $classe->id_classe)
            ->with(['contact', 'tuteurs'])
            ->actifs()
            ->get()
            ->sortBy(fn (Eleve $e) => mb_strtolower($e->contact?->nom.' '.$e->contact?->prenom))
            ->values();

        // Assiduité de l'année scolaire en cours : devant une classe, savoir
        // qui accumule les absences vaut mieux qu'une liste de noms et d'emails.
        $debutAnnee = Carbon::create(now()->month >= 9 ? now()->year : now()->year - 1, 9, 1);

        $assiduite = AbsenceEleve::whereIn('id_eleve', $eleves->pluck('id_eleve'))
            ->where('date_absence', '>=', $debutAnnee)
            ->get()
            ->groupBy('id_eleve')
            ->map(fn ($lignes) => [
                'absences' => $lignes->filter(fn ($a) => $a->nature === AbsenceEleve::NATURE_ABSENCE)->count(),
                'retards' => $lignes->filter(fn ($a) => $a->nature === AbsenceEleve::NATURE_RETARD)->count(),
                'non_justifiees' => $lignes->filter(fn ($a) => ! $a->justifie)->count(),
            ]);

        return view('espace-intervenant.roster', [
            'classe' => $classe,
            'eleves' => $eleves,
            'assiduite' => $assiduite,
            'depuis' => $debutAnnee,
        ]);
    }
}
