<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_types_missions`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesTypesMissions extends Model
{
    protected $table = 'amos_entreprises_types_missions';
    protected $primaryKey = 'id_entreprises_types_missions';
    public $timestamps = false;

    protected $fillable = [
        'type_mission',
    ];
}
