<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_niveaux_echelonnements_lines`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class NiveauxEchelonnementsLines extends Model
{
    protected $table = 'amos_niveaux_echelonnements_lines';
    protected $primaryKey = 'id_niveau_echelonnement_line';
    public $timestamps = false;

    protected $fillable = [
        'id_niveau_echelonnement',
        'titre',
        'montant',
        'ordre_de',
        'options',
    ];
}
