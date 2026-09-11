<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_stats_appels_sortant`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class StatsAppelsSortant extends Model
{
    protected $table = 'amos_stats_appels_sortant';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'date',
        'annee',
        'id_formation',
        'id_statut',
        'abouti',
        'non_abouti',
        'rappel',
        'inscription_jpo',
        'inscription_ea',
        'envoi_email_one_shot',
        'stop_relance',
        'envoi_courrier',
        'rappel_immediat',
        'demande_brochure',
    ];
}
