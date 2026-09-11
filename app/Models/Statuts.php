<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_statuts`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Statuts extends Model
{
    protected $table = 'amos_statuts';
    protected $primaryKey = 'id_statut';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
        'code_status',
        'score',
        'libelle_front',
    ];
}
