<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Détache la notation des unités d'enseignement (suite du passage au K-12).
 *
 * `amos_sn_base_notes` et `amos_sn_evaluations_existantes` imposent toutes
 * deux `id_ue` → `amos_unite_enseignement`. L'unité d'enseignement est un
 * découpage de l'enseignement supérieur : dans une école primaire ou un
 * collège, on note par **matière**, et il n'existe aucune UE à référencer.
 * En l'état, enregistrer la moindre note était donc impossible sans inventer
 * des UE fictives.
 *
 * Les colonnes restent (valeur 0 par défaut) pour le code legacy qui les lit ;
 * seules les contraintes sautent. Les autres clés étrangères — campus,
 * matière, élève, évaluation, type d'évaluation — sont légitimes et
 * conservées : c'est bien la matière, et non l'UE, qui structure la notation.
 *
 * Même démarche que `detache_absences_des_unites_enseignement`.
 */
return new class extends Migration
{
    /** Tables dont la clé étrangère `id_ue` doit sauter. */
    private const TABLES = [
        'amos_sn_base_notes',
        'amos_sn_evaluations_existantes',
    ];

    /** Le nom de la contrainte peut varier d'un serveur à l'autre : on le retrouve. */
    private function nomContrainte(string $table): ?string
    {
        $ligne = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, 'id_ue']
        );

        return $ligne->CONSTRAINT_NAME ?? null;
    }

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if ($nom = $this->nomContrainte($table)) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$nom}`");
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `id_ue` INT(11) NOT NULL DEFAULT 0");
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || $this->nomContrainte($table)) {
                continue;
            }

            // Des notes K-12 pointent une UE inexistante (id_ue = 0) : rétablir
            // la contrainte telle quelle échouerait, on les raccroche d'abord.
            DB::statement("UPDATE `{$table}` t
                LEFT JOIN `amos_unite_enseignement` u ON u.id_unite_enseignement = t.id_ue
                SET t.id_ue = (SELECT MIN(id_unite_enseignement) FROM `amos_unite_enseignement`)
                WHERE u.id_unite_enseignement IS NULL");

            DB::statement("ALTER TABLE `{$table}`
                ADD CONSTRAINT `{$table}_ibfk_ue`
                FOREIGN KEY (`id_ue`) REFERENCES `amos_unite_enseignement` (`id_unite_enseignement`)
                ON DELETE CASCADE ON UPDATE CASCADE");
        }
    }
};
