<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_cron_etablissement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class CronEtablissement extends Model
{
    protected $table = 'amos_cron_etablissement';
    protected $primaryKey = 'id_cron_etablissement';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'etablissement',
        'date_cron',
        'decision',
        'is_sent',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
    ];
}
