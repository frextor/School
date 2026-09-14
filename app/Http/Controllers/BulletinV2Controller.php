<?php

namespace App\Http\Controllers;

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
 * - L'assiduité (`get_assiduite()`, table `absence_eleve`) n'est pas
 *   incluse : module Assiduité non migré.
 */
class BulletinV2Controller extends Controller
{
    public function index(Request $request): View
    {
        $bulletins = SnBulletin::with(['eleve.contact', 'etablissement'])
            ->when($request->filled('id_etablissement'), fn ($q) => $q->where('id_etablissement', $request->integer('id_etablissement')))
            ->when($request->filled('annee'), fn ($q) => $q->where('annee', $request->integer('annee')))
            ->when($request->filled('semestre'), fn ($q) => $q->where('semestre', $request->integer('semestre')))
            ->orderByDesc('date_insert')
            ->limit(30)
            ->get();

        return view('bulletin-v2.index', [
            'bulletins' => $bulletins,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function create(): View
    {
        return view('bulletin-v2.create', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
        ]);
    }

    /** Portage de `get_eleves_by_classe()`. */
    public function studentsForClass(Request $request, Classe $classe): JsonResponse
    {
        $eleves = Eleve::where('id_classe', $classe->id_classe)
            ->when($request->filled('annee_rentree'), fn ($q) => $q->where('annee_rentree', $request->integer('annee_rentree')))
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
            ->groupBy('id_ue');
    }

    /**
     * Portage de `generer()` — calcule les moyennes par UE (moyenne pondérée
     * par coefficient de type d'évaluation) et génère le PDF du bulletin.
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
        $notesParUe = $this->notesEleve($eleve, $data['annee'], $data['semestre'] ?? null);

        $ues = $notesParUe->map(function ($notes, $idUe) {
            $premiere = $notes->first();
            $totalPoints = 0;
            $totalCoef = 0;

            foreach ($notes as $note) {
                $coef = (float) ($note->evaluation?->typeEvaluation?->coef ?? 1);
                $valeur = is_numeric($note->note) ? (float) $note->note : null;

                if ($valeur !== null) {
                    $totalPoints += $valeur * $coef;
                    $totalCoef += $coef;
                }
            }

            return [
                'nom_ue' => $premiere->evaluation?->unite?->nom_unite_enseignement,
                'notes' => $notes,
                'moyenne' => $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : null,
            ];
        })->values();

        // Moyenne générale = moyenne simple des moyennes d'UE (pas de pondération ECTS :
        // dépend de referentiel_classe, volontairement différé — voir en-tête de classe).
        $moyennesUe = $ues->pluck('moyenne')->filter(fn ($m) => $m !== null);

        $pdf = Pdf::loadView('bulletin-v2.pdf', [
            'eleve' => $eleve,
            'ues' => $ues,
            'annee' => $data['annee'],
            'semestre' => $data['semestre'] ?? null,
            'session' => $data['session'] ?? 0,
            'etablissement' => Etablissement::find($data['id_etablissement']),
            'moyenneGenerale' => $moyennesUe->isNotEmpty() ? round($moyennesUe->avg(), 2) : null,
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
