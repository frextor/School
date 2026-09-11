<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_locks`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Locks extends Model
{
    protected $table = 'amos_locks';
    protected $primaryKey = 'id_lock';
    public $timestamps = false;

    protected $fillable = [
        'module',
        'page',
        'id_admin',
        'SESSIONID',
        'date',
    ];
}
