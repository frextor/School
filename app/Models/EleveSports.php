<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_sports`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveSports extends Model
{
    protected $table = 'amos_eleve_sports';
    protected $primaryKey = 'id_eleve_sport';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'sport',
        'niveau',
        'palmares',
    ];
}
