<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Dernière dépendance de la notation aux unités d'enseignement :
 * `amos_sn_type_evaluation.id_unite_enseignement`.
 *
 * Complète `detache_absences_des_unites_enseignement` et
 * `detache_notation_des_unites_enseignement`. Ces contraintes ont été
 * rencontrées une par une en testant le parcours complet de notation K-12
 * (créer un type d'évaluation → une évaluation → une note → un bulletin) :
 * cette migration balaie donc **toute** contrainte restante vers
 * `amos_unite_enseignement`, plutôt que d'en nommer une de plus et de risquer
 * d'en découvrir une quatrième en production.
 *
 * Les colonnes sont conservées (valeur 0 par défaut) pour le code legacy.
 */
return new class extends Migration
{
    private function contraintesVersUe(): array
    {
        return DB::select(
            "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND REFERENCED_TABLE_NAME = 'amos_unite_enseignement'"
        );
    }

    public function up(): void
    {
        foreach ($this->contraintesVersUe() as $fk) {
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` MODIFY `{$fk->COLUMN_NAME}` INT(11) NOT NULL DEFAULT 0");
        }
    }

    public function down(): void
    {
        // Irréversible par construction : les contraintes supprimées ici
        // portaient des noms et des tables variables, et les données K-12
        // créées depuis référencent des UE inexistantes (id = 0). Les
        // migrations précédentes savent restaurer les leurs ; celle-ci n'a
        // pas de cible fixe à rétablir.
    }
};
