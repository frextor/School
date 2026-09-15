<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Échéancier de scolarité d'un élève (frais d'inscription + mensualités).
 *
 * Aucune structure legacy ne convenait :
 * - `amos_paiement_eleve` / `amos_cheques_paiement` enregistrent ce qui **a été
 *   payé**, pas ce qui **reste dû à une date donnée** — sans quoi la notion
 *   d'impayé n'existe pas ;
 * - `amos_niveaux_echelonnements(_lines)` sont des gabarits par niveau, pas des
 *   échéances d'élève, avec `montant` en `int` (pas de centimes) et une date
 *   stockée en `varchar(10)`.
 *
 * Les noms de colonnes `date_echeance` / `montant` et les accesseurs `moyen` /
 * `encaisse` du modèle suivent la forme attendue par l'onglet « Règlements »
 * de la fiche élève, qui peut ainsi l'afficher sans adaptation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances', function (Blueprint $table) {
            $table->id('id_echeance');
            // `amos_eleves.id_eleve` est un int(11) signé : même type, sans clé
            // étrangère (la table legacy est en latin1/InnoDB avec ses propres
            // contraintes, on évite d'y ajouter une dépendance croisée).
            $table->integer('id_eleve')->index();
            $table->string('annee_scolaire', 9);
            $table->string('type', 20)->default('mensualite');
            $table->string('libelle', 100);
            $table->decimal('montant', 10, 2);
            $table->date('date_echeance');
            $table->decimal('montant_regle', 10, 2)->default(0);
            $table->date('date_reglement')->nullable();
            $table->string('mode_reglement', 30)->nullable();
            $table->string('commentaire', 250)->nullable();
            $table->timestamps();

            // Un même libellé ne peut exister qu'une fois par élève et par année :
            // protège la génération d'échéancier contre les doublons.
            $table->unique(['id_eleve', 'annee_scolaire', 'libelle'], 'echeances_unicite');
            $table->index(['annee_scolaire', 'date_echeance']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};
