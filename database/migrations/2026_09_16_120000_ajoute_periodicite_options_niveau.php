<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Périodicité d'une option facturable : annuelle (assurance, frais de
 * dossier) ou mensuelle (transport scolaire, cantine, garderie).
 *
 * Le catalogue legacy `amos_niveaux_options` n'a qu'un montant : il
 * servait au module « règlements » du supérieur, où une option se facturait
 * une fois. En école marocaine, les options les plus courantes se paient
 * chaque mois avec la mensualité — sans périodicité, impossible de les
 * intégrer à l'échéancier.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amos_niveaux_options', function (Blueprint $table) {
            $table->string('periodicite', 10)->default('annuelle')->after('montant');
        });
    }

    public function down(): void
    {
        Schema::table('amos_niveaux_options', function (Blueprint $table) {
            $table->dropColumn('periodicite');
        });
    }
};
