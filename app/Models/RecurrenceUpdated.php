<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_recurrence_updated`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class RecurrenceUpdated extends Model
{
    protected $table = 'amos_recurrence_updated';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_recurrence',
        'date_recurrence',
        'data_recurrence',
    ];
}
