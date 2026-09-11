<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_recurrence`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielRecurrence extends Model
{
    protected $table = 'amos_referentiel_recurrence';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_recurrence',
        'type_recurrence',
        'fin_recurrence',
        'count_recurrence',
        'days_recurrence',
        'months_recurrence',
        'days_of_month',
        'occurences',
        'type',
    ];
}
