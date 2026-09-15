<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Élargit `amos_sn_bulletins.id_etablissement` et `id_niveau` de TINYINT à INT.
 *
 * Ces deux colonnes référencent des clés primaires `int(11)` mais sont
 * déclarées `tinyint(11) unsigned` — un plafond à 255, quelle que soit la
 * largeur d'affichage indiquée. Au-delà, l'enregistrement d'un bulletin
 * échoue avec « Numeric value out of range » : le PDF est généré puis perdu.
 *
 * Le problème avait été jugé théorique tant que le produit visait un
 * établissement unique aux identifiants bas. Il ne l'est plus : le passage
 * aux écoles marocaines a créé de nouveaux niveaux (identifiants déjà au-delà
 * de 30 en production) et vise plusieurs établissements, dont les
 * identifiants AUTO_INCREMENT franchiront 255 au fil de la vie du produit.
 *
 * Aucune perte possible : on élargit un type, on ne le rétrécit pas.
 */
return new class extends Migration
{
    private const COLONNES = ['id_etablissement', 'id_niveau'];

    /**
     * MySQL revalide toute la définition de la table à chaque ALTER. Or
     * `date_update` porte un défaut `0000-00-00 00:00:00`, hérité du schéma
     * legacy et refusé par le mode strict : sans neutraliser le sql_mode, la
     * modification d'une colonne sans rapport échoue sur ce défaut.
     */
    private function sansModeStrict(callable $operations): void
    {
        $modeInitial = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode;

        DB::statement("SET SESSION sql_mode = ''");

        try {
            $operations();
        } finally {
            DB::statement('SET SESSION sql_mode = ?', [$modeInitial]);
        }
    }

    public function up(): void
    {
        if (! Schema::hasTable('amos_sn_bulletins')) {
            return;
        }

        $this->sansModeStrict(function () {
            foreach (self::COLONNES as $colonne) {
                DB::statement("ALTER TABLE `amos_sn_bulletins` MODIFY `{$colonne}` INT(11) UNSIGNED NOT NULL");
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('amos_sn_bulletins')) {
            return;
        }

        // Revenir à TINYINT tronquerait les bulletins dont la référence dépasse
        // 255 : on ne rétrécit que si aucune valeur ne le dépasse.
        foreach (self::COLONNES as $colonne) {
            $max = (int) DB::table('amos_sn_bulletins')->max($colonne);

            if ($max <= 255) {
                $this->sansModeStrict(fn () => DB::statement(
                    "ALTER TABLE `amos_sn_bulletins` MODIFY `{$colonne}` TINYINT(11) UNSIGNED NOT NULL"
                ));
            }
        }
    }
};
