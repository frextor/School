<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_stats_transformations_abandonnistes`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class StatsTransformationsAbandonnistes extends Model
{
    protected $table = 'amos_stats_transformations_abandonnistes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'date',
        'annee',
        'id_formation',
        'id_famille',
        'id_motif',
        'prospects',
        'demandes_brochure',
        'inscrits_jpo',
        'participants_jpo',
        'inscrits_ea',
        'participants_ea',
        'admis',
        'inscrits',
    ];
}
