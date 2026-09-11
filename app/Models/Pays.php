<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_pays`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Pays extends Model
{
    protected $table = 'amos_pays';
    protected $primaryKey = 'id_pays';
    public $timestamps = false;

    protected $fillable = [
        'code_pays',
        'fr',
        'en',
        'es',
    ];
}
