<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_eleves_periodes_trimestrielles`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesElevesPeriodesTrimestrielles extends Model
{
    protected $table = 'amos_entreprises_eleves_periodes_trimestrielles';
    protected $primaryKey = 'id_periode';
    public $timestamps = false;

    protected $fillable = [
        'periode',
        'nb_heure',
        'id_entreprises_eleves',
    ];
}
