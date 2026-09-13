<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table Laravel "moderne" (pas de préfixe amos_, pas de lien avec le schéma
 * legacy) : formulaire de demande de démo sur la landing page (nouvelle
 * fonctionnalité, sans équivalent CodeIgniter).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_demo', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('etablissement');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->text('message')->nullable();
            $table->boolean('traitee')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_demo');
    }
};
