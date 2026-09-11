<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_locks_messages`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class LocksMessages extends Model
{
    protected $table = 'amos_locks_messages';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'message',
        'SESSIONID',
        'date',
    ];
}
