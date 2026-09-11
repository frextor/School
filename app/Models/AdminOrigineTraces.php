<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_admin_origine_traces`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class AdminOrigineTraces extends Model
{
    protected $table = 'amos_admin_origine_traces';
    protected $primaryKey = 'id_admin_origine_trace';
    public $timestamps = false;

    protected $fillable = [
        'id_admin',
        'id_contact',
        'module',
        'trace',
        'espace',
        'utilisateur',
        'date',
    ];
}
