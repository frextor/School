<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_nationalites`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Nationalites extends Model
{
    protected $table = 'amos_nationalites';
    protected $primaryKey = 'id_nationalite';
    public $timestamps = false;

    protected $fillable = [
        'code_pays',
        'fr',
    ];
}
