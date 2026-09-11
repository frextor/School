<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
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

    public function show(Eleve $candidat): View
    {
        $candidat->load(['contact', 'niveau', 'epreuvesInscriptions.epreuve', 'resultatsEpreuves.epreuve']);

        return view('candidats.show', ['candidat' => $candidat]);
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
