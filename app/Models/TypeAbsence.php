<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_type_absence`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class TypeAbsence extends Model
{
    protected $table = 'amos_type_absence';
    protected $primaryKey = 'id_type_absence';
    public $timestamps = false;

    protected $fillable = [
        'nom_type_absence',
    ];
}
