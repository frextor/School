<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_panneaux_classes`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class PanneauxClasses extends Model
{
    protected $table = 'amos_panneaux_classes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_panneau',
        'id_classe',
    ];
}
