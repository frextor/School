<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Echeance;
use App\Models\Eleve;
use App\Models\NiveauxOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Échéanciers de scolarité : frais d'inscription + mensualités, et suivi des
 * impayés — le modèle de facturation des écoles privées marocaines.
 *
 * Complète le module « règlements » existant, qui enregistre ce qui a été payé
 * mais ne décrit pas ce qui est dû : sans échéance datée, aucun impayé
 * détectable.
 */
class EcheanceController extends Controller
{
    public function index(Request $request): View
    {
        $annee = $request->string('annee')->toString() ?: Echeance::anneeScolaireCourante();

        $echeances = Echeance::query()
            ->with(['eleve.contact', 'eleve.classe'])
            ->annee($annee)
            ->when($request->filled('classe'), fn ($q) => $q->whereHas('eleve', fn ($q) => $q->where('id_classe', $request->integer('classe'))))
            ->when($request->boolean('impayees'), fn ($q) => $q->impayees())
            ->orderBy('date_echeance')
            ->paginate(40)
            ->withQueryString();

        // Totaux calculés en base sur l'ensemble du filtre, pas sur la page courante.
        $base = Echeance::query()
            ->annee($annee)
            ->when($request->filled('classe'), fn ($q) => $q->whereHas('eleve', fn ($q) => $q->where('id_classe', $request->integer('classe'))));

        $du = (float) (clone $base)->sum('montant');
        $regle = (float) (clone $base)->sum('montant_regle');

        return view('echeances.index', [
            'echeances' => $echeances,
            'classes' => Classe::orderBy('classe')->get(),
            'annee' => $annee,
            'annees' => Echeance::query()->distinct()->orderByDesc('annee_scolaire')->pluck('annee_scolaire'),
            'filtres' => $request->only(['classe', 'impayees']),
            'totaux' => [
                'du' => $du,
                'regle' => $regle,
                'reste' => max(0, $du - $regle),
                'impayees' => (clone $base)->impayees()->count(),
            ],
        ]);
    }

    public function formulaire(Request $request): View
    {
        $annee = Echeance::anneeScolaireCourante();

        return view('echeances.generer', [
            'classes' => Classe::orderBy('classe')->get(['id_classe', 'classe', 'id_niveau']),
            'annee' => $annee,
            'classeChoisie' => $request->integer('classe'),
            // Catalogue d'options de l'année, groupé par niveau : la vue n'affiche
            // que celles du niveau de la classe (ou de l'élève) choisi.
            'optionsParNiveau' => NiveauxOptions::where('annee', (int) substr($annee, 0, 4))
                ->orderBy('ordre')
                ->get()
                ->groupBy('id_niveau')
                ->map(fn ($opts) => $opts->map(fn ($o) => [
                    'id' => $o->id_niveau_option,
                    'titre' => $o->titre,
                    'montant' => (float) $o->montant,
                    'mensuelle' => $o->mensuelle,
                ])->values()),
        ]);
    }

    /**
     * Génère l'échéancier d'une classe entière (cas courant : même tarif pour
     * tous) ou d'un élève. Les élèves ayant déjà un échéancier sur cette année
     * sont ignorés, sauf demande explicite de remplacement — on ne veut pas
     * écraser des règlements déjà encaissés par une regénération distraite.
     */
    public function generer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cible' => ['required', 'in:classe,eleve'],
            'classe' => ['required_if:cible,classe', 'nullable', 'integer', 'exists:amos_classe,id_classe'],
            'id_eleve' => ['required_if:cible,eleve', 'nullable', 'integer', 'exists:amos_eleves,id_eleve'],
            'annee_scolaire' => ['required', 'string', 'max:9'],
            'frais_inscription' => ['nullable', 'numeric', 'min:0'],
            'date_inscription' => ['nullable', 'date'],
            'montant_mensualite' => ['required', 'numeric', 'min:0'],
            'nb_mensualites' => ['required', 'integer', 'min:1', 'max:12'],
            'mois_debut' => ['required', 'integer', 'min:1', 'max:12'],
            'jour_echeance' => ['required', 'integer', 'min:1', 'max:28'],
            'remplacer' => ['boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['integer', 'exists:amos_niveaux_options,id_niveau_option'],
            'montant_option' => ['nullable', 'array'],
            'montant_option.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Options cochées, avec le montant éventuellement ajusté dans le formulaire.
        $options = NiveauxOptions::whereIn('id_niveau_option', $data['options'] ?? [])->get()
            ->map(function (NiveauxOptions $o) use ($data) {
                $o->montant_applique = (float) (($data['montant_option'][$o->id_niveau_option] ?? null) ?: $o->montant);

                return $o;
            });

        $eleves = $data['cible'] === 'classe'
            ? Eleve::where('id_classe', $data['classe'])->where('profil', Eleve::PROFIL_ELEVE)->where('visible', true)->get()
            : Eleve::where('id_eleve', $data['id_eleve'])->get();

        if ($eleves->isEmpty()) {
            return back()->withErrors(['classe' => 'Aucun élève actif pour cette sélection.']);
        }

        $anneeDebut = (int) substr($data['annee_scolaire'], 0, 4);
        $remplacer = $request->boolean('remplacer');
        $crees = 0;
        $ignores = 0;

        DB::transaction(function () use ($data, $eleves, $anneeDebut, $remplacer, $options, &$crees, &$ignores) {
            foreach ($eleves as $eleve) {
                $existe = Echeance::where('id_eleve', $eleve->id_eleve)
                    ->where('annee_scolaire', $data['annee_scolaire'])
                    ->exists();

                if ($existe && ! $remplacer) {
                    $ignores++;

                    continue;
                }

                if ($existe) {
                    // Remplacement demandé : on ne touche pas aux échéances déjà
                    // réglées, même partiellement, pour ne pas perdre d'encaissement.
                    Echeance::where('id_eleve', $eleve->id_eleve)
                        ->where('annee_scolaire', $data['annee_scolaire'])
                        ->where('montant_regle', 0)
                        ->delete();
                }

                if (! empty($data['frais_inscription']) && $data['frais_inscription'] > 0) {
                    Echeance::firstOrCreate(
                        [
                            'id_eleve' => $eleve->id_eleve,
                            'annee_scolaire' => $data['annee_scolaire'],
                            'libelle' => "Frais d'inscription",
                        ],
                        [
                            'type' => Echeance::TYPE_INSCRIPTION,
                            'montant' => $data['frais_inscription'],
                            // Un champ facultatif non soumis est absent du tableau validé,
                            // d'où le `??` avant le `?:` (une chaîne vide est aussi possible).
                            'date_echeance' => ($data['date_inscription'] ?? null)
                                ?: Carbon::create($anneeDebut, $data['mois_debut'], $data['jour_echeance']),
                        ]
                    );
                    $crees++;
                }

                // Première mensualité : les mois de janvier à juillet appartiennent
                // à la seconde année civile de l'année scolaire (2026-2027 :
                // septembre 2026 → juin 2027). Les suivantes se déduisent par
                // simple décalage, qui gère le passage d'année de lui-même.
                $premiere = Carbon::create(
                    $data['mois_debut'] >= 8 ? $anneeDebut : $anneeDebut + 1,
                    $data['mois_debut'],
                    $data['jour_echeance']
                );

                for ($i = 0; $i < $data['nb_mensualites']; $i++) {
                    $date = $premiere->copy()->addMonthsNoOverflow($i);
                    $libelle = 'Mensualité '.$date->locale('fr_FR')->isoFormat('MMMM YYYY');

                    Echeance::firstOrCreate(
                        [
                            'id_eleve' => $eleve->id_eleve,
                            'annee_scolaire' => $data['annee_scolaire'],
                            'libelle' => $libelle,
                        ],
                        [
                            'type' => Echeance::TYPE_MENSUALITE,
                            'montant' => $data['montant_mensualite'],
                            'date_echeance' => $date,
                        ]
                    );
                    $crees++;
                }

                // Options facturables : une ligne par mois pour les options mensuelles
                // (transport, cantine…), une seule ligne pour les annuelles (assurance…).
                // Lignes distinctes des mensualités : la famille voit ce qu'elle paie,
                // et une option peut être arrêtée en cours d'année sans toucher au reste.
                foreach ($options as $option) {
                    if ($option->mensuelle) {
                        for ($i = 0; $i < $data['nb_mensualites']; $i++) {
                            $date = $premiere->copy()->addMonthsNoOverflow($i);
                            Echeance::firstOrCreate(
                                [
                                    'id_eleve' => $eleve->id_eleve,
                                    'annee_scolaire' => $data['annee_scolaire'],
                                    'libelle' => $option->titre.' — '.$date->locale('fr_FR')->isoFormat('MMMM YYYY'),
                                ],
                                ['type' => Echeance::TYPE_OPTION, 'montant' => $option->montant_applique, 'date_echeance' => $date]
                            );
                            $crees++;
                        }
                    } else {
                        Echeance::firstOrCreate(
                            [
                                'id_eleve' => $eleve->id_eleve,
                                'annee_scolaire' => $data['annee_scolaire'],
                                'libelle' => $option->titre,
                            ],
                            ['type' => Echeance::TYPE_OPTION, 'montant' => $option->montant_applique, 'date_echeance' => $premiere]
                        );
                        $crees++;
                    }
                }
            }
        });

        $message = "{$crees} échéance(s) générée(s) pour {$eleves->count()} élève(s)";
        $message .= $ignores ? ", {$ignores} élève(s) ignoré(s) (échéancier déjà existant)." : '.';

        return redirect()
            ->route('echeances.index', ['annee' => $data['annee_scolaire']])
            ->with('status', $message);
    }

    /** Enregistre un règlement sur une échéance (total ou partiel). */
    public function regler(Request $request, Echeance $echeance): RedirectResponse
    {
        $data = $request->validate([
            'montant_regle' => ['required', 'numeric', 'min:0'],
            'date_reglement' => ['nullable', 'date'],
            'mode_reglement' => ['nullable', 'string', 'max:30'],
            'commentaire' => ['nullable', 'string', 'max:250'],
        ]);

        $echeance->update([
            'montant_regle' => $data['montant_regle'],
            'date_reglement' => $data['montant_regle'] > 0 ? (($data['date_reglement'] ?? null) ?: now()) : null,
            'mode_reglement' => $data['mode_reglement'] ?? null,
            'commentaire' => $data['commentaire'] ?? null,
        ]);

        return back()->with('status', "Règlement enregistré : {$echeance->statut_libelle}.");
    }

    public function destroy(Echeance $echeance): RedirectResponse
    {
        if ((float) $echeance->montant_regle > 0) {
            return back()->withErrors(['echeance' => 'Impossible de supprimer une échéance déjà réglée — retirez d\'abord le règlement.']);
        }

        $echeance->delete();

        return back()->with('status', 'Échéance supprimée.');
    }
}
