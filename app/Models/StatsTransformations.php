<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_stats_transformations`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class StatsTransformations extends Model
{
    protected $table = 'amos_stats_transformations';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'date',
        'annee',
        'id_formation',
        'id_famille',
        'prospects',
        'demandes_brochure',
        'inscrits_jpo',
        'participants_jpo',
        'non_participants_jpo',
        'inscrits_ea',
        'paiement_ea',
        'non_paiement_ea',
        'participants_ea',
        'non_participants_ea',
        'admis',
        'inscrits_partiel',
        'inscrits',
        'reinscrits_partiel',
        'reinscrits',
    ];
}
