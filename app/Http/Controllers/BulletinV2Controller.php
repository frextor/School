<?php

namespace App\Http\Controllers;

use App\Models\AbsenceEleve;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Niveau;
use App\Models\Note;
use App\Models\SnBulletin;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Portage de `BulletinV2.php` (CodeIgniter) — le système de bulletins
 * "moderne", basé sur les mêmes tables `sn_*` que Notation.php déjà migré
 * (`amos_sn_base_notes`, `amos_sn_evaluations_existantes`,
 * `amos_sn_type_evaluation`).
 *
 * Simplifications et exclusions assumées :
 * - Rendu PDF via `barryvdh/laravel-dompdf` (ajouté avec l'accord de
 *   l'utilisateur) plutôt que la lib `BulletinV2_pdf` (TCPDF) legacy.
 * - Les ECTS par UE (`get_ects()` legacy) ne sont pas affichés : ils
 *   dépendent de la table `referentiel_classe` (association cours↔classe),
 *   volontairement différée avec le reste de Referentiel.php.
 * - L'assiduité est désormais incluse (absences, absences non justifiées,
 *   retards), le module Assiduité ayant été ajouté depuis.
 * - Orientation K-12 : les notes sont regroupées par **matière** et non plus
 *   par unité d'enseignement (découpage propre au supérieur), et la moyenne
 *   générale est pondérée par le coefficient de matière défini sur le niveau.
 */
class BulletinV2Controller extends Controller
{
    /**
     * Écran de consultation des bulletins : on choisit une population
     * (campus / niveau / classe) et une période (année scolaire / semestre /
     * session), et la recherche renvoie la **liste des élèves** concernés avec,
     * pour chacun, le bulletin déjà généré pour cette période s'il existe.
     *
     * Auparavant cet écran listait les 30 derniers PDF générés, ce qui ne
     * permettait pas de répondre à la question posée au quotidien : « qui,
     * dans cette classe, n'a pas encore son bulletin du semestre 1 ? ».
     */
    public function index(Request $request): View
    {
        $filtres = [
            'campus' => $request->filled('campus') ? $request->integer('campus') : null,
            'niveau' => $request->filled('niveau') ? $request->integer('niveau') : null,
            'classe' => $request->filled('classe') ? $request->integer('classe') : null,
            'annee' => $request->filled('annee') ? $request->integer('annee') : null,
            'semestre' => $request->filled('semestre') ? $request->integer('semestre') : null,
            'session' => $request->filled('session') ? $request->integer('session') : null,
        ];

        // La liste d'élèves n'apparaît qu'après une recherche explicite : sans
        // cela l'écran afficherait les 400 élèves de l'école sans contexte.
        $recherche = $request->has('recherche');
        $eleves = null;
        $bulletinsParEleve = collect();

        if ($recherche) {
            $eleves = Eleve::query()
                ->with(['contact', 'niveau', 'classe.etablissement'])
                ->select('amos_eleves.*')
                // Jointure contact pour trier par nom (la relation Eloquent ne
                // suffit pas), même approche que la liste des élèves.
                ->leftJoin('amos_contacts', 'amos_contacts.id_contact', '=', 'amos_eleves.id_contact')
                ->where('amos_eleves.visible', true)
                ->where('amos_eleves.profil', '!=', Eleve::PROFIL_CANDIDAT)
                // Campus : porté par la classe (amos_eleves n'a pas de colonne
                // établissement), niveau et classe portés par l'élève.
                ->when($filtres['campus'], fn ($q, $v) => $q->whereHas('classe', fn ($c) => $c->where('id_etablissement', $v)))
                ->when($filtres['niveau'], fn ($q, $v) => $q->where('amos_eleves.id_niveau', $v))
                ->when($filtres['classe'], fn ($q, $v) => $q->where('amos_eleves.id_classe', $v))
                ->orderBy('amos_contacts.nom')
                ->orderBy('amos_contacts.prenom')
                ->paginate(50)
                ->withQueryString();

            // Bulletin de la période demandée, par élève : le plus récent gagne
            // (la table garde l'historique des regénérations).
            $bulletinsParEleve = SnBulletin::query()
                ->whereIn('id_eleve', collect($eleves->items())->pluck('id_eleve'))
                ->when($filtres['annee'], fn ($q, $v) => $q->where('annee', $v))
                ->when($filtres['semestre'] !== null, fn ($q) => $q->where('semestre', $filtres['semestre']))
                ->when($filtres['session'] !== null, fn ($q) => $q->where('session', $filtres['session']))
                ->orderByDesc('date_insert')
                ->get()
                ->groupBy('id_eleve')
                ->map(fn ($groupe) => $groupe->first());
        }

        // Années proposées : celles réellement présentes en base, complétées par
        // l'année scolaire en cours pour pouvoir consulter une période vierge.
        $annees = SnBulletin::query()->distinct()->orderByDesc('annee')->pluck('annee')
            ->push((int) date('Y'))
            ->unique()
            ->sortDesc()
            ->values();

        return view('bulletin-v2.index', [
            'recherche' => $recherche,
            'filtres' => $filtres,
            'eleves' => $eleves,
            'bulletinsParEleve' => $bulletinsParEleve,
            'annees' => $annees,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(['id_classe', 'classe', 'id_niveau', 'id_etablissement']),
            // Sans recherche, l'écran reste utile : derniers PDF produits.
            'derniers' => $recherche
                ? collect()
                : SnBulletin::with(['eleve.contact', 'etablissement'])->orderByDesc('date_insert')->limit(10)->get(),
        ]);
    }

    public function create(): View
    {
        return view('bulletin-v2.create', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            // Chargées à plat puis filtrées côté client par niveau (cascade
            // niveau -> classe -> élève, voir studentsForClass) : pas besoin
            // d'ID élève à saisir à la main.
            'classes' => Classe::orderBy('classe')->get(['id_classe', 'classe', 'id_niveau']),
            // Encadré « Derniers bulletins » du formulaire.
            'bulletins' => SnBulletin::with('eleve.contact')->orderByDesc('date_insert')->limit(5)->get(),
        ]);
    }

    /** Portage de `get_eleves_by_classe()`. */
    public function studentsForClass(Request $request, Classe $classe): JsonResponse
    {
        $eleves = Eleve::where('id_classe', $classe->id_classe)
            // `annee_rentree` est porté par le contact, pas par `amos_eleves` :
            // filtrer directement sur l'élève levait une erreur SQL.
            ->when($request->filled('annee_rentree'), fn ($q) => $q->whereHas('contact', fn ($c) => $c->where('annee_rentree', $request->integer('annee_rentree'))))
            ->with('contact')
            ->get()
            ->map(fn (Eleve $e) => [
                'id_eleve' => $e->id_eleve,
                'nom' => $e->contact?->nom,
                'prenom' => $e->contact?->prenom,
            ]);

        return response()->json($eleves);
    }

    /** Portage de `get_evaluation()` — notes brutes groupées par UE/cours pour un élève. */
    private function notesEleve(Eleve $eleve, int $annee, ?int $semestre): \Illuminate\Support\Collection
    {
        return Note::with(['evaluation.unite', 'evaluation.matiere', 'evaluation.typeEvaluation'])
            ->where('id_eleve', $eleve->id_eleve)
            ->where('annee', $annee)
            ->when($semestre, fn ($q) => $q->where('semestre', $semestre))
            ->get()
            ->groupBy('id_matiere');
    }

    /**
     * Récapitulatif d'assiduité de la période du bulletin.
     * Les absences existent depuis le module Assiduité ; le bulletin les
     * affiche désormais, ce que l'en-tête de cette classe annonçait comme
     * impossible tant que le module n'existait pas.
     */
    private function assiduite(Eleve $eleve, int $annee, ?int $semestre): array
    {
        $absences = AbsenceEleve::where('id_eleve', $eleve->id_eleve)
            ->when($semestre, fn ($q) => $q->where('semestre', $semestre))
            ->whereYear('date_absence', '>=', $annee)
            ->whereYear('date_absence', '<=', $annee + 1)
            ->get();

        return [
            'absences' => $absences->filter(fn ($a) => $a->nature === AbsenceEleve::NATURE_ABSENCE)->count(),
            'absences_non_justifiees' => $absences->filter(fn ($a) => $a->nature === AbsenceEleve::NATURE_ABSENCE && ! $a->justifie)->count(),
            'retards' => $absences->filter(fn ($a) => $a->nature === AbsenceEleve::NATURE_RETARD)->count(),
        ];
    }

    /**
     * Portage de `generer()` — calcule la moyenne de chaque matière (pondérée
     * par le coefficient du type d'évaluation) puis la moyenne générale
     * (pondérée par le coefficient de matière du niveau), et génère le PDF.
     */
    public function generate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_eleve' => ['required', 'integer', 'exists:amos_eleves,id_eleve'],
            'annee' => ['required', 'integer'],
            'semestre' => ['nullable', 'integer'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'id_niveau' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'session' => ['nullable', 'integer'],
        ]);

        $eleve = Eleve::with('contact')->findOrFail($data['id_eleve']);
        $notesParMatiere = $this->notesEleve($eleve, $data['annee'], $data['semestre'] ?? null);

        // Coefficients de matière définis pour le niveau du bulletin (K-12).
        $coefficients = Niveau::find($data['id_niveau'])?->matieres
            ->mapWithKeys(fn ($m) => [$m->id_cours => (float) $m->pivot->coefficient])
            ?? collect();

        $matieres = $notesParMatiere->map(function ($notes, $idMatiere) use ($coefficients) {
            $premiere = $notes->first();
            $totalPoints = 0;
            $totalCoef = 0;

            // Moyenne de la matière : pondérée par le coefficient du **type**
            // d'évaluation (un examen pèse plus qu'un contrôle continu).
            foreach ($notes as $note) {
                $coef = (float) ($note->evaluation?->typeEvaluation?->coef ?? 1);
                $valeur = is_numeric($note->note) ? (float) $note->note : null;

                if ($valeur !== null) {
                    $totalPoints += $valeur * $coef;
                    $totalCoef += $coef;
                }
            }

            return [
                'nom_matiere' => $premiere->evaluation?->matiere?->nom_cours ?? 'Matière',
                'notes' => $notes,
                'coefficient' => $coefficients[$idMatiere] ?? 1.0,
                'moyenne' => $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : null,
            ];
        })->sortBy('nom_matiere')->values();

        // Moyenne générale : pondérée par le coefficient de chaque **matière**
        // (Maths coef 4, Éducation islamique coef 1...), comme un bulletin marocain.
        // Les matières sans note ne comptent pas, sinon elles tireraient la moyenne vers le bas.
        $notees = $matieres->filter(fn ($m) => $m['moyenne'] !== null);
        $sommeCoefs = $notees->sum('coefficient');
        $moyenneGenerale = $sommeCoefs > 0
            ? round($notees->sum(fn ($m) => $m['moyenne'] * $m['coefficient']) / $sommeCoefs, 2)
            : null;

        $pdf = Pdf::loadView('bulletin-v2.pdf', [
            'eleve' => $eleve,
            'matieres' => $matieres,
            'annee' => $data['annee'],
            'semestre' => $data['semestre'] ?? null,
            'session' => $data['session'] ?? 0,
            'etablissement' => Etablissement::find($data['id_etablissement']),
            'moyenneGenerale' => $moyenneGenerale,
            'assiduite' => $this->assiduite($eleve, $data['annee'], $data['semestre'] ?? null),
        ]);

        $bulletin = SnBulletin::create([
            'pdf' => $pdf->output(),
            'annee' => $data['annee'],
            'semestre' => $data['semestre'] ?? 0,
            'id_etablissement' => $data['id_etablissement'],
            'id_eleve' => $eleve->id_eleve,
            'id_niveau' => $data['id_niveau'],
            'session' => $data['session'] ?? 0,
            'active' => true,
        ]);

        return redirect()
            ->route('bulletin-v2.index')
            ->with('status', "Bulletin généré pour {$eleve->contact?->nom_complet} (n°{$bulletin->id_bulletin}).");
    }

    /** Portage de `bulletin($id_bulletin)` — téléchargement du PDF stocké. */
    public function show(SnBulletin $bulletin): Response
    {
        return response($bulletin->pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="bulletin-'.$bulletin->id_bulletin.'.pdf"',
        ]);
    }
}
