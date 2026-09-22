<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Formation;
use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Référentiel pédagogique : pour chaque niveau, les matières enseignées,
 * leur coefficient et leur volume horaire hebdomadaire.
 *
 * Remplace le portage de `Ref.php` (grille cc/cr/td/ei + ECTS par unité
 * d'enseignement). Cette grille était inutilisable ici : elle imposait de
 * choisir une **unité d'enseignement**, découpage du supérieur écarté du
 * K-12 marocain — la table `amos_unite_enseignement` est vide en local
 * comme en production, donc la liste déroulante l'était aussi et aucune
 * ligne ne pouvait être saisie ni affichée. Les tables et modèles
 * `ReferentielNiveau` / `ReferentielClasse` sont conservés, seuls l'écran
 * et ses routes d'écriture changent de socle.
 *
 * La source de vérité est `matiere_niveau`, déjà utilisée par la notation
 * et les bulletins pour pondérer la moyenne générale : le référentiel
 * n'introduit pas une seconde table concurrente, il donne à celle-ci
 * l'écran d'ensemble qui lui manquait (elle n'était éditable que niveau
 * par niveau, depuis un onglet de la fiche d'un niveau).
 *
 * NON repris du legacy, comme dans le portage précédent : le référentiel
 * par groupe d'élèves (`referentiel_groupe`), le verrouillage collaboratif
 * temps réel (`check_lock` / SSE) et la grille JS type tableur qui envoyait
 * tout le référentiel d'un coup en JSON.
 */
class ReferentielController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        // Seuls les cycles qui portent au moins un niveau : `amos_formations`
        // garde des cycles du supérieur hérités du legacy (Master 2…) qui
        // n'ont plus de niveau et n'ont rien à faire dans le filtre.
        $cycles = Formation::has('niveaux')->orderBy('priorite')->orderBy('niveau')->get();
        $recherche = trim((string) $request->string('recherche'));

        $niveaux = Niveau::query()
            ->with(['formation', 'matieres'])
            ->withCount('classes')
            ->when($request->filled('cycle'), fn ($q) => $q->where('id_formation', $request->integer('cycle')))
            ->when($request->filled('niveau'), fn ($q) => $q->where('id_niveau', $request->integer('niveau')))
            ->get()
            // Tri par cycle (priorité de `amos_formations`) puis par niveau :
            // l'ordre de lecture d'une grille horaire, de la PS à la 2BAC.
            ->sortBy(fn (Niveau $n) => sprintf('%02d|%s', $n->formation?->priorite ?? 99, $n->nom_niveau))
            ->values();

        // La recherche porte sur la matière : on garde les niveaux qui
        // l'enseignent, en ne montrant que les lignes concernées.
        if ($recherche !== '') {
            $niveaux = $niveaux->filter(function (Niveau $niveau) use ($recherche) {
                $lignes = $niveau->matieres->filter(
                    fn ($matiere) => mb_stripos($matiere->nom_cours, $recherche) !== false
                );
                $niveau->setRelation('matieres', $lignes->values());

                return $lignes->isNotEmpty();
            })->values();
        }

        if ($request->boolean('export')) {
            return $this->exporterCsv($niveaux);
        }

        return view('referentiel.ref.index', [
            'cycles' => $cycles,
            'niveaux' => $niveaux,
            // Listés dans l'ordre de la scolarité, pas par ordre alphabétique :
            // « 1ère année baccalauréat » avant « 1ère année collégiale » n'aide personne.
            'niveauxTous' => Niveau::with('formation')->orderBy('nom_niveau')->get()
                ->sortBy(fn (Niveau $n) => sprintf('%02d|%s', $n->formation?->priorite ?? 99, $n->nom_niveau))
                ->groupBy(fn (Niveau $n) => $n->formation?->niveau ?: 'Sans cycle'),
            'matieres' => Cours::orderBy('nom_cours')->get(),
            'recherche' => $recherche,
            'stats' => $this->statistiques($niveaux),
        ]);
    }

    /** Chiffres de l'en-tête, calculés sur ce que l'écran affiche réellement. */
    private function statistiques(Collection $niveaux): array
    {
        $lignes = $niveaux->flatMap->matieres;

        return [
            'niveaux' => $niveaux->count(),
            'couverts' => $niveaux->filter(fn ($n) => $n->matieres->isNotEmpty())->count(),
            'matieres' => $lignes->pluck('id_cours')->unique()->count(),
            'lignes' => $lignes->count(),
            'heures' => $lignes->sum(fn ($m) => (float) $m->pivot->volume_horaire),
            'sansHeures' => $lignes->filter(fn ($m) => (float) $m->pivot->volume_horaire <= 0)->count(),
        ];
    }

    private function exporterCsv(Collection $niveaux): StreamedResponse
    {
        return response()->streamDownload(function () use ($niveaux) {
            $sortie = fopen('php://output', 'w');
            // BOM : sans lui Excel lit le CSV en ANSI et casse les accents.
            fwrite($sortie, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($sortie, ['Cycle', 'Niveau', 'Code', 'Rang', 'Matière', 'Coefficient', 'Heures/semaine'], ';');

            foreach ($niveaux as $niveau) {
                foreach ($niveau->matieres as $matiere) {
                    fputcsv($sortie, [
                        $niveau->formation?->niveau ?? '',
                        $niveau->nom_niveau,
                        $niveau->code_niveau,
                        $matiere->pivot->ordre,
                        $matiere->nom_cours,
                        $matiere->pivot->coefficient,
                        $matiere->pivot->volume_horaire,
                    ], ';');
                }
            }

            fclose($sortie);
        }, 'referentiel-pedagogique-'.date('Ymd_Hi').'.csv', ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    /**
     * Enregistre en une fois toutes les lignes d'un niveau (coefficient,
     * volume horaire, rang d'affichage). Une ligne par requête obligeait à
     * valider matière après matière alors qu'on règle un référentiel d'un bloc.
     */
    public function enregistrer(Request $request, Niveau $niveau): RedirectResponse
    {
        $data = $request->validate([
            'lignes' => ['array'],
            'lignes.*.coefficient' => ['required', 'numeric', 'min:0.5', 'max:20'],
            'lignes.*.volume_horaire' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'lignes.*.ordre' => ['nullable', 'integer', 'min:1', 'max:255'],
        ]);

        $rattachees = $niveau->matieres()->pluck('amos_cours.id_cours')->all();

        foreach ($data['lignes'] ?? [] as $idCours => $ligne) {
            // On n'écrit que sur les matières réellement rattachées : le
            // formulaire est en clair dans la page, l'identifiant aussi.
            if (! in_array((int) $idCours, $rattachees, true)) {
                continue;
            }

            $niveau->matieres()->updateExistingPivot((int) $idCours, [
                'coefficient' => $ligne['coefficient'],
                'volume_horaire' => $ligne['volume_horaire'] ?? 0,
                'ordre' => $ligne['ordre'] ?? 1,
            ]);
        }

        return back()->with('status', "Référentiel de « {$niveau->nom_niveau} » enregistré.");
    }

    /**
     * Recopie le référentiel d'un niveau vers d'autres niveaux.
     *
     * Les six années du primaire partagent la même liste de matières :
     * sans cette reprise (`cloner()` du legacy), il fallait ressaisir huit
     * lignes six fois de suite.
     */
    public function dupliquer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'source' => ['required', 'integer', 'exists:amos_niveaux,id_niveau'],
            'cibles' => ['required', 'array', 'min:1'],
            'cibles.*' => ['integer', 'exists:amos_niveaux,id_niveau'],
            'mode' => ['required', 'in:completer,remplacer'],
        ]);

        $source = Niveau::with('matieres')->findOrFail($data['source']);

        if ($source->matieres->isEmpty()) {
            return back()->withErrors(['source' => 'Ce niveau n\'a aucune matière à recopier.']);
        }

        $lignes = $source->matieres->mapWithKeys(fn ($matiere) => [$matiere->id_cours => [
            'coefficient' => $matiere->pivot->coefficient,
            'volume_horaire' => $matiere->pivot->volume_horaire,
            'ordre' => $matiere->pivot->ordre,
        ]])->all();

        $cibles = array_values(array_diff(array_map('intval', $data['cibles']), [$source->id_niveau]));

        if ($cibles === []) {
            return back()->withErrors(['cibles' => 'Choisissez au moins un niveau différent du niveau source.']);
        }

        DB::transaction(function () use ($cibles, $lignes, $data) {
            foreach (Niveau::whereIn('id_niveau', $cibles)->get() as $cible) {
                // « Compléter » laisse intacts les coefficients déjà réglés,
                // « remplacer » aligne le niveau cible sur la source.
                $data['mode'] === 'remplacer'
                    ? $cible->matieres()->sync($lignes)
                    : $cible->matieres()->syncWithoutDetaching($lignes);
            }
        });

        return back()->with('status', count($cibles) === 1
            ? 'Référentiel recopié sur 1 niveau.'
            : 'Référentiel recopié sur '.count($cibles).' niveaux.');
    }
}
