<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_param_sources_connaitre_ecole`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ParamSourcesConnaitreEcole extends Model
{
    protected $table = 'amos_param_sources_connaitre_ecole';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nom_source',
    ];
}
