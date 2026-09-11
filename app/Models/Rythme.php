<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_rythme`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Rythme extends Model
{
    protected $table = 'amos_rythme';
    protected $primaryKey = 'id_rythme';
    public $timestamps = false;

    protected $fillable = [
        'nom_rythme',
    ];
}
