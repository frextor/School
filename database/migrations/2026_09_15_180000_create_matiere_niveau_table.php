<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Coefficient d'une matière pour un niveau donné.
 *
 * Au Maroc, la moyenne générale est pondérée par un coefficient propre à
 * chaque matière, et ce coefficient varie selon le niveau (les mathématiques
 * ne pèsent pas la même chose en 3AEP et en 2BAC sciences maths). Sans lui,
 * la moyenne générale ne peut être qu'une moyenne simple des matières, ce qui
 * est faux pour un bulletin marocain.
 *
 * Rien dans le schéma legacy ne portait cette notion :
 * - `amos_sn_type_evaluation.coef` pondère le **type d'évaluation**
 *   (contrôle continu vs examen) au sein d'une matière, pas les matières
 *   entre elles ;
 * - `amos_referentiel_niveau` associe bien cours et niveau, mais pour y
 *   stocker des **volumes horaires** (cc/cr/td/ei), pas un coefficient.
 *
 * La matière est un `amos_cours` (c'est la table que `id_matiere` référence
 * partout dans la notation).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matiere_niveau', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cours');
            $table->integer('id_niveau');
            $table->decimal('coefficient', 5, 2)->default(1);
            $table->unsignedTinyInteger('ordre')->default(1);
            $table->timestamps();

            $table->unique(['id_cours', 'id_niveau'], 'matiere_niveau_unicite');
            $table->index('id_niveau');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matiere_niveau');
    }
};
