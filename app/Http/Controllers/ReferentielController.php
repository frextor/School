<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Etablissement;
use App\Models\Intervenant;
use App\Models\Niveau;
use App\Models\ReferentielClasse;
use App\Models\ReferentielNiveau;
use App\Models\UniteEnseignement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Ref.php` (référentiel des heures d'enseignement : quel cours,
 * avec quel intervenant, pour combien d'heures (CC/CR/TD/EI) et combien
 * d'ECTS, par niveau ou par classe, pour une UE et une année données).
 *
 * Périmètre volontairement réduit par rapport au legacy (~900 lignes) :
 * - Chaque ligne est ajoutée/supprimée individuellement via un formulaire
 *   classique, au lieu de la grille JS type tableur qui envoie tout le
 *   référentiel d'un coup en JSON (`save()`/`groupes_save()`).
 * - Le référentiel "groupe" (`referentiel_groupe`, variante par groupe
 *   d'élèves plutôt que par niveau/classe) n'est pas repris : périmètre
 *   déjà couvert fonctionnellement par la variante "classe".
 * - Le verrouillage collaboratif temps réel (`check_lock`/SSE `sse()`) —
 *   empêchant deux admins de modifier le même référentiel en même temps —
 *   n'est pas repris : fonctionnalité de confort, pas de règle métier.
 * - Le clonage d'un référentiel d'un établissement/UE/année vers un autre
 *   (`cloner`/`cloner_groupes`) et l'export CSV (`export_referentiel`) ne
 *   sont pas repris dans cette passe.
 */
class ReferentielController extends Controller
{
    public function index(Request $request): View
    {
        $idEtablissement = $request->integer('id_etablissement') ?: (int) session('referentiel_etablissement');
        $idUe = $request->integer('id_unite_enseignement') ?: (int) session('referentiel_ue');
        $annee = $request->string('annee')->toString() ?: (string) session('referentiel_annee');

        if ($request->has('id_etablissement')) {
            session([
                'referentiel_etablissement' => $idEtablissement,
                'referentiel_ue' => $idUe,
                'referentiel_annee' => $annee,
            ]);
        }

        $lignesNiveau = collect();
        $lignesClasse = collect();

        if ($idEtablissement && $idUe && $annee !== '') {
            $lignesNiveau = ReferentielNiveau::query()
                ->with(['niveau', 'cours', 'intervenant'])
                ->where('id_etablissement', $idEtablissement)
                ->where('id_unite_enseignement', $idUe)
                ->where('anne', $annee)
                ->orderBy('semestre')
                ->get();

            $lignesClasse = ReferentielClasse::query()
                ->with(['niveau', 'cours', 'intervenant', 'classe'])
                ->where('id_etablissement', $idEtablissement)
                ->where('id_unite_enseignement', $idUe)
                ->where('anne', $annee)
                ->orderBy('semestre')
                ->get();
        }

        return view('referentiel.ref.index', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'unites' => UniteEnseignement::orderBy('nom_unite_enseignement')->get(),
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => $idEtablissement ? Classe::where('id_etablissement', $idEtablissement)->orderBy('classe')->get() : collect(),
            'cours' => $idUe ? Cours::where('id_unite_enseignement', $idUe)->orderBy('nom_cours')->get() : collect(),
            'intervenants' => Intervenant::orderBy('nom')->get(),
            'idEtablissement' => $idEtablissement,
            'idUe' => $idUe,
            'annee' => $annee,
            'lignesNiveau' => $lignesNiveau,
            'lignesClasse' => $lignesClasse,
        ]);
    }

    public function storeNiveau(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_etablissement' => ['required', 'integer'],
            'id_unite_enseignement' => ['required', 'integer'],
            'annee' => ['required', 'string', 'max:12'],
            'id_niveau' => ['required', 'integer'],
            'semestre' => ['required', 'string', 'max:3'],
            'id_cours' => ['required', 'integer'],
            'id_intervenant' => ['required', 'integer'],
            'cc' => ['nullable', 'numeric'],
            'cr' => ['nullable', 'numeric'],
            'td' => ['nullable', 'numeric'],
            'ei' => ['nullable', 'numeric'],
            'volume' => ['nullable', 'numeric'],
            'ects' => ['nullable', 'numeric'],
        ]);

        $data['anne'] = $data['annee'];
        unset($data['annee']);

        ReferentielNiveau::create($this->completerHeures($data));

        return back()->with('status', 'Ligne de référentiel (niveau) ajoutée.');
    }

    public function storeClasse(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_etablissement' => ['required', 'integer'],
            'id_unite_enseignement' => ['required', 'integer'],
            'annee' => ['required', 'integer'],
            'id_niveau' => ['required', 'integer'],
            'id_classe' => ['required', 'integer'],
            'semestre' => ['required', 'string', 'max:45'],
            'id_cours' => ['required', 'integer'],
            'id_intervenant' => ['required', 'integer'],
            'cc' => ['nullable', 'numeric'],
            'cr' => ['nullable', 'numeric'],
            'td' => ['nullable', 'numeric'],
            'ei' => ['nullable', 'numeric'],
            'volume' => ['nullable', 'numeric'],
            'ects' => ['nullable', 'numeric'],
        ]);

        $data['anne'] = $data['annee'];
        unset($data['annee']);

        $data = $this->completerHeures($data);
        $data['modifie'] = 0;
        $data['ignore'] = 0;
        $data['id_referentiel_niveau'] = 0;

        ReferentielClasse::create($data);

        return back()->with('status', 'Ligne de référentiel (classe) ajoutée.');
    }

    public function destroyNiveau(ReferentielNiveau $referentielNiveau): RedirectResponse
    {
        $referentielNiveau->delete();

        return back()->with('status', 'Ligne de référentiel (niveau) supprimée.');
    }

    public function destroyClasse(ReferentielClasse $referentielClasse): RedirectResponse
    {
        $referentielClasse->delete();

        return back()->with('status', 'Ligne de référentiel (classe) supprimée.');
    }

    /** Les colonnes d'heures/valeurs sont NOT NULL sans défaut côté legacy : on complète à 0. */
    private function completerHeures(array $data): array
    {
        foreach (['cc', 'cr', 'td', 'ei', 'cc_val', 'cr_val', 'td_val', 'ei_val', 'volume', 'ects'] as $champ) {
            $data[$champ] = $data[$champ] ?? 0;
        }

        return $data;
    }
}
