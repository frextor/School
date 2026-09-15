<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Parents / tuteurs légaux (orientation K-12 marocaine).
 *
 * Volontairement **hors schéma legacy** : `amos_eleve_infos_parents` rattache
 * un parent à un seul élève (`id_eleve` en colonne), ce qui duplique le parent
 * pour chaque enfant d'une même fratrie. Insoutenable pour la suite :
 * - mise à jour d'un téléphone à faire autant de fois qu'il y a d'enfants ;
 * - à l'ouverture des comptes parents, aucun moyen de savoir quelle ligne
 *   dupliquée porte le compte.
 *
 * On modélise donc le tuteur comme une entité à part entière, reliée aux
 * élèves par une table de liaison qui porte le lien de parenté. Un parent de
 * trois enfants = une fiche, trois liaisons.
 *
 * Nommé `tuteurs` et non `parents` : `Parent` est un mot réservé de PHP, un
 * modèle Eloquent ne peut pas porter ce nom.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuteurs', function (Blueprint $table) {
            $table->id('id_tuteur');
            $table->string('civilite', 10)->nullable();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 150)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('telephone_pro', 30)->nullable();
            $table->string('profession', 100)->nullable();
            // Pièce d'identité : demandée dans les dossiers d'inscription au Maroc.
            $table->string('cin', 30)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('code_postal', 20)->nullable();
            $table->string('ville', 100)->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index(['nom', 'prenom']);
        });

        Schema::create('eleve_tuteur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tuteur');
            // `amos_eleves.id_eleve` est un int(11) signé : on garde le même type.
            $table->integer('id_eleve');
            $table->string('lien_parente', 30)->default('pere');
            // Responsable légal : signe les autorisations, reçoit les bulletins.
            $table->boolean('responsable_legal')->default(true);
            // Personne à prévenir en cas d'urgence (peut différer du responsable légal).
            $table->boolean('contact_urgence')->default(false);
            $table->unsignedTinyInteger('ordre')->default(1);
            $table->timestamps();

            // Un même tuteur ne peut être rattaché qu'une fois au même élève.
            $table->unique(['id_tuteur', 'id_eleve']);
            $table->index('id_eleve');

            $table->foreign('id_tuteur')->references('id_tuteur')->on('tuteurs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eleve_tuteur');
        Schema::dropIfExists('tuteurs');
    }
};
