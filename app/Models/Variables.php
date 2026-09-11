<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_variables`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Variables extends Model
{
    protected $table = 'amos_variables';
    protected $primaryKey = 'name';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
    ];
}
