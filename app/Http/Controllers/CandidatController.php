<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Niveau;
use App\Models\ResultatEpreuveEleve;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Candidats.php` (CodeIgniter) — liste et fiche des candidats
 * (élèves avec `profil = 'candidat'`).
 *
 * Simplifications et exclusions assumées :
 * - Les listes spécialisées (`liste_candidats_no_epreuve`, exports) ne sont
 *   pas dupliquées ; `index()` accepte un filtre équivalent.
 * - `supprimer()` (legacy) nettoie une dizaine de sous-tables (séjours,
 *   sports, expériences, langues, règlements, user espace-élève...) non
 *   toutes migrées : ce portage supprime le candidat et ses inscriptions
 *   aux épreuves d'admission, le reste sera nettoyé au fil de la migration
 *   des modules correspondants.
 * - Voir `EpreuveAdmissionController` pour la gestion des épreuves.
 */
class CandidatController extends Controller
{
    public function index(Request $request): View
    {
        $candidats = Eleve::query()
            ->candidats()
            ->with(['contact', 'niveau'])
            ->when($request->boolean('sans_epreuve'), fn ($q) => $q->doesntHave('epreuvesInscriptions'))
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->whereHas('contact', fn ($q) => $q->where('nom', 'like', "%{$terme}%")->orWhere('prenom', 'like', "%{$terme}%"));
            })
            // Portage de `Archives.php::liste()` : bascule liste active / archivée
            // plutôt qu'un écran dupliqué (voir MIGRATION_PROGRESS.md).
            ->where('visible', $request->boolean('archives'))
            ->orderByDesc('id_eleve')
            ->paginate(25)
            ->withQueryString();

        return view('candidats.index', ['candidats' => $candidats, 'filtres' => $request->only(['recherche', 'sans_epreuve', 'archives'])]);
    }

    /** Décisions valant admission (voir ResultatEpreuveEleve::DECISION_*) — la seule condition pour inscrire un candidat comme élève. */
    private const DECISIONS_ADMIS = [
        ResultatEpreuveEleve::DECISION_ACCEPTE,
        ResultatEpreuveEleve::DECISION_ACCEPTE_NIVEAU_INFERIEUR,
        ResultatEpreuveEleve::DECISION_ACCEPTE_AVEC_ENTREPRISE,
    ];

    public function show(Eleve $candidat): View
    {
        $candidat->load(['contact', 'niveau', 'epreuvesInscriptions.epreuve', 'resultatsEpreuves.epreuve']);

        return view('candidats.show', [
            'candidat' => $candidat,
            'estAdmis' => $candidat->resultatsEpreuves->contains(fn ($r) => in_array($r->decision, self::DECISIONS_ADMIS, true)),
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(['id_classe', 'classe', 'id_niveau']),
        ]);
    }

    /**
     * Portage du passage manuel candidat -> élève (pas d'équivalent legacy direct :
     * le legacy recalculait le profil automatiquement au règlement du premier
     * versement — simplifié ici en action explicite, cf. tête de classe pour le
     * reste des simplifications du module paiements).
     */
    public function inscrireEleve(Request $request, Eleve $candidat): RedirectResponse
    {
        if ($candidat->profil !== Eleve::PROFIL_CANDIDAT) {
            return back()->withErrors(['profil' => 'Ce dossier n\'est plus un candidat.']);
        }

        $estAdmis = $candidat->resultatsEpreuves()
            ->whereIn('decision', self::DECISIONS_ADMIS)
            ->exists();

        if (! $estAdmis) {
            return back()->withErrors(['decision' => "Ce candidat n'a pas de résultat d'admission favorable — impossible de l'inscrire comme élève."]);
        }

        $data = $request->validate([
            'id_niveau' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'id_classe' => ['required', 'integer', 'exists:amos_classe,id_classe'],
        ]);

        $candidat->update([
            'profil' => Eleve::PROFIL_ELEVE,
            'id_niveau' => $data['id_niveau'],
            'id_classe' => $data['id_classe'],
            'visible' => true, // convention Eleve (différente de Candidat !) : visible=true = actif, visible=false = masqué (voir EleveController::destroy)
            'date_inscription' => $candidat->date_inscription ?? now(),
        ]);

        return redirect()
            ->route('eleves.show', $candidat)
            ->with('status', "{$candidat->contact?->nom_complet} est maintenant inscrit comme élève.");
    }

    /** Portage de `archiver()`. */
    public function archive(Eleve $candidat): RedirectResponse
    {
        $candidat->update(['visible' => true]);

        return back()->with('status', 'Candidat archivé.');
    }

    /** Portage de `archiver_attente_epreuve()`. */
    public function archiveAttenteEpreuve(Eleve $candidat): RedirectResponse
    {
        $candidat->update(['visible_attente_epreuve' => true]);

        return back()->with('status', "Marqué en attente d'épreuve.");
    }

    /** Portage de `non_archiver()`. */
    public function unarchive(Eleve $candidat): RedirectResponse
    {
        $candidat->update(['visible' => false]);

        return back()->with('status', 'Candidat désarchivé.');
    }

    /** Portage de `supprimer()` (voir note de classe sur le périmètre). */
    public function destroy(Eleve $candidat): RedirectResponse
    {
        $candidat->epreuvesInscriptions()->delete();
        $candidat->resultatsEpreuves()->delete();
        $candidat->delete();

        return redirect()->route('candidats.index')->with('status', 'Candidat supprimé.');
    }
}
