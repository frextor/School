<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Niveau;
use App\Models\TypeEvaluation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Référentiel scolaire marocain (K-12) : les 4 cycles et leurs niveaux.
 *
 * Réutilise le schéma legacy tel quel :
 * - `amos_formations` porte le **cycle** (Maternelle / Primaire / Collège / Lycée) ;
 * - `amos_niveaux` porte le **niveau** rattaché à un cycle, avec `id_niveau_future`
 *   qui chaîne le passage automatique de fin d'année (PS → MS → … → 2BAC).
 *
 * Idempotent : relancer le seeder ne crée pas de doublon (clé = `code_niveau`
 * pour les niveaux, libellé pour les cycles) et re-chaîne simplement les niveaux.
 *
 * Les filières de lycée (Sciences Expérimentales, Sciences Maths, Lettres…) ne
 * sont volontairement pas des niveaux : elles se portent au niveau de la
 * **classe** (ex. « 2BAC SVT A »), qui est déjà du texte libre.
 */
class ReferentielMarocSeeder extends Seeder
{
    /**
     * Cycle => [priorité, [code niveau => nom du niveau] dans l'ordre de progression].
     */
    private const CYCLES = [
        'Maternelle' => [1, [
            'PS' => 'Petite Section',
            'MS' => 'Moyenne Section',
            'GS' => 'Grande Section',
        ]],
        'Primaire' => [2, [
            '1AEP' => '1ère année primaire',
            '2AEP' => '2ème année primaire',
            '3AEP' => '3ème année primaire',
            '4AEP' => '4ème année primaire',
            '5AEP' => '5ème année primaire',
            '6AEP' => '6ème année primaire',
        ]],
        'Collège' => [3, [
            '1AC' => '1ère année collégiale',
            '2AC' => '2ème année collégiale',
            '3AC' => '3ème année collégiale',
        ]],
        'Lycée' => [4, [
            'TC' => 'Tronc commun',
            '1BAC' => '1ère année baccalauréat',
            '2BAC' => '2ème année baccalauréat',
        ]],
    ];

    public function run(): void
    {
        // Types d'évaluation du système marocain (liste portée par le modèle,
        // qui la propose aussi sur l'écran du dictionnaire). Sans au moins un
        // type, aucune évaluation ne peut être créée (contrainte sur
        // `amos_sn_evaluations_existantes`), donc aucune note saisie : une
        // installation neuve était inutilisable.
        foreach (TypeEvaluation::TYPES_MAROC as $type) {
            DB::table('amos_type_evaluation')->updateOrInsert(['type' => $type]);
        }

        DB::transaction(function () {
            // Ordre de progression global, tous cycles confondus : sert à chaîner
            // `id_niveau_future` d'un cycle au suivant (GS → 1AEP, 6AEP → 1AC…).
            $progression = [];

            foreach (self::CYCLES as $nomCycle => [$priorite, $niveaux]) {
                $cycle = Formation::firstOrCreate(
                    ['niveau' => $nomCycle],
                    ['description' => "Cycle {$nomCycle}", 'priorite' => $priorite]
                );

                foreach ($niveaux as $code => $nom) {
                    $niveau = Niveau::firstOrCreate(
                        ['code_niveau' => $code],
                        [
                            'nom_niveau' => $nom,
                            'id_formation' => $cycle->id_formation,
                            'id_niveau_future' => 0,
                            'deuxieme_langue' => 0,
                        ]
                    );

                    // Un niveau déjà présent (relance du seeder) peut avoir été
                    // rattaché au mauvais cycle : on réaligne sans écraser son nom.
                    if ($niveau->id_formation !== $cycle->id_formation) {
                        $niveau->update(['id_formation' => $cycle->id_formation]);
                    }

                    $progression[] = $niveau;
                }
            }

            // Chaînage du passage de fin d'année : chaque niveau pointe le suivant,
            // le dernier (2BAC) reste à 0 = fin de cursus.
            foreach ($progression as $i => $niveau) {
                $suivant = $progression[$i + 1] ?? null;
                $niveau->update(['id_niveau_future' => $suivant?->id_niveau ?? 0]);
            }

            $this->command?->info(count($progression).' niveaux marocains en place, répartis sur '.count(self::CYCLES).' cycles.');
        });
    }
}
