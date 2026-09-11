<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_recurrence_deleted`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class RecurrenceDeleted extends Model
{
    protected $table = 'amos_recurrence_deleted';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_recurrence',
        'date_excepted',
    ];
}
