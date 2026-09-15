<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Détache `amos_absence_eleve` des unités d'enseignement.
 *
 * La table impose `id_unite_enseignement` → `amos_unite_enseignement`
 * (NOT NULL + clé étrangère). L'unité d'enseignement est un découpage propre
 * à l'enseignement supérieur : en K-12, une séance est identifiée par sa date,
 * son heure et éventuellement sa matière — il n'existe aucune UE à référencer.
 * Conserver la contrainte obligerait à créer des UE fictives juste pour
 * pouvoir saisir une absence.
 *
 * La colonne est conservée (valeur 0 par défaut) pour ne pas casser le code
 * legacy qui la lit encore ; seule la contrainte saute. Accessoirement, son
 * ON DELETE CASCADE était dangereux : supprimer une UE effaçait les absences.
 *
 * La clé étrangère sur `id_eleve`, elle, est légitime et reste en place.
 */
return new class extends Migration
{
    /** Retrouve le nom réel de la contrainte : il peut varier d'un serveur à l'autre. */
    private function nomContrainte(): ?string
    {
        $ligne = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['amos_absence_eleve', 'id_unite_enseignement']
        );

        return $ligne->CONSTRAINT_NAME ?? null;
    }

    public function up(): void
    {
        if (! Schema::hasTable('amos_absence_eleve')) {
            return;
        }

        if ($nom = $this->nomContrainte()) {
            DB::statement("ALTER TABLE `amos_absence_eleve` DROP FOREIGN KEY `{$nom}`");
        }

        DB::statement('ALTER TABLE `amos_absence_eleve` MODIFY `id_unite_enseignement` INT(11) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        if (! Schema::hasTable('amos_absence_eleve') || $this->nomContrainte()) {
            return;
        }

        // Rétablir la contrainte échouerait si des absences pointent une UE
        // inexistante (cas normal en K-12) : on nettoie d'abord ces références.
        DB::statement('UPDATE `amos_absence_eleve` a
            LEFT JOIN `amos_unite_enseignement` u ON u.id_unite_enseignement = a.id_unite_enseignement
            SET a.id_unite_enseignement = (SELECT MIN(id_unite_enseignement) FROM `amos_unite_enseignement`)
            WHERE u.id_unite_enseignement IS NULL');

        DB::statement('ALTER TABLE `amos_absence_eleve`
            ADD CONSTRAINT `amos_absence_eleve_ibfk_2`
            FOREIGN KEY (`id_unite_enseignement`) REFERENCES `amos_unite_enseignement` (`id_unite_enseignement`)
            ON DELETE CASCADE ON UPDATE CASCADE');
    }
};
