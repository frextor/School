<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\Niveau;
use App\Models\VolumesFormations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Parametrage.php` : volumes horaires prévisionnels par
 * niveau/établissement (effectif, nombre de classes, volume de cours),
 * utilisés pour dimensionner les besoins d'une année de formation.
 *
 * Périmètre volontairement réduit par rapport au legacy (~586 lignes) :
 * - Une seule page de saisie/consultation (par établissement), au lieu de
 *   trois écrans séparés (`volumes_formations`/`volumes_prof_eleve`/
 *   `volumes_profs`) chargés en AJAX avec DataTables.
 * - Les totaux Bachelor/Master sont calculés génériquement via
 *   `Formation::IDS_BACHELOR`/`IDS_MASTER` (déjà utilisées ailleurs dans le
 *   projet), au lieu de la comparaison de libellés de niveau câblés en dur
 *   et fragiles du legacy (`'Bachelor 1ère année'`, etc., avec des artefacts
 *   d'encodage dans le code source d'origine).
 * - `volumes_profs_etablissement()`/`get_volumes_profs()` (croisement par
 *   intervenant, doublon du référentiel des heures déjà couvert par
 *   `Ref.php`/`ReferentielController`) ne sont pas repris.
 */
class ParametrageController extends Controller
{
    public function index(Request $request): View
    {
        $idEtablissement = $request->integer('id_etablissement') ?: (int) session('parametrage_etablissement');

        if ($request->has('id_etablissement')) {
            session(['parametrage_etablissement' => $idEtablissement]);
        }

        $niveaux = Niveau::orderBy('nom_niveau')->get();
        $volumes = collect();
        $totaux = ['bachelor' => ['effectif' => 0, 'nb_classe' => 0, 'volume_cours' => 0], 'master' => ['effectif' => 0, 'nb_classe' => 0, 'volume_cours' => 0]];

        if ($idEtablissement) {
            $volumes = VolumesFormations::query()
                ->with('niveau')
                ->where('id_etablissement', $idEtablissement)
                ->get()
                ->keyBy('id_niveau');

            foreach ($volumes as $volume) {
                $idFormation = $volume->niveau?->id_formation;
                $cle = in_array($idFormation, Formation::IDS_MASTER, true) ? 'master' : 'bachelor';
                $totaux[$cle]['effectif'] += (int) $volume->effectif;
                $totaux[$cle]['nb_classe'] += (int) $volume->nb_classe;
                $totaux[$cle]['volume_cours'] += (float) $volume->volume_cours;
            }
        }

        return view('parametrage.index', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'niveaux' => $niveaux,
            'volumes' => $volumes,
            'totaux' => $totaux,
            'idEtablissement' => $idEtablissement,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_etablissement' => ['required', 'integer'],
            'lignes' => ['array'],
            'lignes.*.id_niveau' => ['required', 'integer'],
            'lignes.*.effectif' => ['nullable', 'integer'],
            'lignes.*.nb_classe' => ['nullable', 'integer'],
            'lignes.*.volume_cours' => ['nullable', 'numeric'],
        ]);

        foreach ($data['lignes'] ?? [] as $ligne) {
            if (($ligne['effectif'] ?? '') === '' && ($ligne['nb_classe'] ?? '') === '' && ($ligne['volume_cours'] ?? '') === '') {
                continue;
            }

            VolumesFormations::updateOrCreate(
                ['id_etablissement' => $data['id_etablissement'], 'id_niveau' => $ligne['id_niveau']],
                [
                    'effectif' => $ligne['effectif'] ?? 0,
                    'nb_classe' => $ligne['nb_classe'] ?? 0,
                    'volume_cours' => $ligne['volume_cours'] ?? 0,
                ]
            );
        }

        return redirect()->route('referentiel.parametrage.index', ['id_etablissement' => $data['id_etablissement']])
            ->with('status', 'Volumes de formation enregistrés.');
    }
}
