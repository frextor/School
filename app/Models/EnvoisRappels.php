<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_envois_rappels`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EnvoisRappels extends Model
{
    protected $table = 'amos_envois_rappels';
    protected $primaryKey = 'id_eleve';
    public $timestamps = true;

    protected $fillable = [
        'id_eleve',
        'rappel_admission_1',
        'rappel_admission_2',
        'rappel_admission_3',
    ];
}
