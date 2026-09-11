<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_niveaux_frais`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class NiveauxFrais extends Model
{
    protected $table = 'amos_niveaux_frais';
    protected $primaryKey = 'id_niveau_option';
    public $timestamps = false;

    protected $fillable = [
        'id_niveau',
        'titre',
        'montant',
        'ordre',
    ];
}
