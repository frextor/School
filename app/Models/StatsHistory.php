<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_stats_history`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class StatsHistory extends Model
{
    protected $table = 'amos_stats_history';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'date',
        'annee',
        'id_formation',
        'id_contact',
        'segment',
        'columns',
        'op',
        'number',
        'id_famille',
        'id_statut',
        'id_motif',
    ];
}
