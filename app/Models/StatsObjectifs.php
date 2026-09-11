<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_stats_objectifs`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class StatsObjectifs extends Model
{
    protected $table = 'amos_stats_objectifs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'date',
        'annee',
        'id_formation',
        'objectif',
        'realise',
    ];
}
