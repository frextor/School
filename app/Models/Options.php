<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_options`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Options extends Model
{
    protected $table = 'amos_options';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'option',
        'valeur',
    ];
}
