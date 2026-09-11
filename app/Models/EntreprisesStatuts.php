<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_statuts`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesStatuts extends Model
{
    protected $table = 'amos_entreprises_statuts';
    protected $primaryKey = 'id_entreprises_statuts';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
        'libelle_front',
        'code_status',
        'score',
    ];
}
