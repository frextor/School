<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_api_keys`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ApiKeys extends Model
{
    protected $table = 'amos_api_keys';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'key',
        'level',
        'ignore_limits',
        'date_created',
    ];
}
