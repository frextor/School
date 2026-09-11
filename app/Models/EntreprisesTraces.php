<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_traces`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesTraces extends Model
{
    protected $table = 'amos_entreprises_traces';
    protected $primaryKey = 'id_entreprises_traces';
    public $timestamps = false;

    protected $fillable = [
        'id_entreprise',
        'trace',
        'espace',
        'utilisateur',
        'date',
    ];
}
