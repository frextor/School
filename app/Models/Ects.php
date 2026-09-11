<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_ects`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Ects extends Model
{
    protected $table = 'amos_ects';
    protected $primaryKey = 'id_ects';
    public $timestamps = false;

    protected $fillable = [
        'credit_ects',
        'id_unite_enseignement',
        'id_niveau',
    ];
}
