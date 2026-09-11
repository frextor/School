<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_regles`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Regles extends Model
{
    protected $table = 'amos_regles';
    protected $primaryKey = 'id_regle';
    public $timestamps = false;

    protected $fillable = [
        'code_regle',
        'libelle',
    ];
}
