<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_etablissement_regles_assiduite`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EtablissementReglesAssiduite extends Model
{
    protected $table = 'amos_etablissement_regles_assiduite';
    protected $primaryKey = 'id_regle_assiduite';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'nom_regle_assiduite',
    ];
}
