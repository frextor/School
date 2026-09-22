<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Volume horaire hebdomadaire d'une matière pour un niveau donné.
 *
 * Le référentiel marocain fixe, pour chaque niveau, non seulement le
 * coefficient d'une matière mais aussi son nombre d'heures par semaine
 * (l'arabe pèse 8 h en 1AEP et 2 h en 2BAC sciences). Sans cette colonne,
 * l'écran « Référentiel pédagogique » ne pouvait afficher qu'un coefficient,
 * et la ligne de menu « heures par matière » ne tenait pas sa promesse.
 *
 * `amos_referentiel_niveau` portait bien des volumes (cc/cr/td/ei) mais
 * indexés par unité d'enseignement, notion absente du K-12 : la table est
 * vide en local comme en production.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matiere_niveau', function (Blueprint $table) {
            $table->decimal('volume_horaire', 4, 1)->default(0)->after('coefficient');
        });
    }

    public function down(): void
    {
        Schema::table('matiere_niveau', function (Blueprint $table) {
            $table->dropColumn('volume_horaire');
        });
    }
};
