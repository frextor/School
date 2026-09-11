<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_dettes_eleve`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class DettesEleve extends Model
{
    protected $table = 'amos_dettes_eleve';
    protected $primaryKey = 'id_dette';
    public $timestamps = false;

    protected $fillable = [
        'formation',
        'montants',
        'solde_du',
        'id_eleve',
    ];
}
