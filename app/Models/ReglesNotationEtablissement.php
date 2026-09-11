<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_regles_notation_etablissement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReglesNotationEtablissement extends Model
{
    protected $table = 'amos_regles_notation_etablissement';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_regle',
        'id_etablissement',
    ];
}
