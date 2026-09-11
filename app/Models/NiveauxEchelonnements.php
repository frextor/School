<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_niveaux_echelonnements`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class NiveauxEchelonnements extends Model
{
    protected $table = 'amos_niveaux_echelonnements';
    protected $primaryKey = 'id_niveau_echelonnement';
    public $timestamps = false;

    protected $fillable = [
        'id_niveau',
        'date',
    ];
}
