<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_constants`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Constants extends Model
{
    protected $table = 'amos_constants';
    protected $primaryKey = 'id_constant';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'value',
        'label',
    ];
}
