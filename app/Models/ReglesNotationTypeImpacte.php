<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_regles_notation_type_impacte`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReglesNotationTypeImpacte extends Model
{
    protected $table = 'amos_regles_notation_type_impacte';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_regle',
        'id_type',
    ];
}
