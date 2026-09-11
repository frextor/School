<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_config_periode_formation`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ConfigPeriodeFormation extends Model
{
    protected $table = 'amos_config_periode_formation';
    protected $primaryKey = 'id_config_periode_formation';
    public $timestamps = false;

    protected $fillable = [
        'annee_scolaire',
        'periode',
        'nb_heure_annuel',
        'nb_heure_trimestriel',
        'diplome_rncp',
        'code_diplome',
    ];
}
