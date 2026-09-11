<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_watching`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Watching extends Model
{
    protected $table = 'amos_watching';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'env',
        'id_admin',
        'id_contact',
        'id_intervenant',
        'id_entreprise',
        'type',
        'value',
        'data_before',
        'element',
        'module',
        'icon',
        'message',
        'message_technique',
        'date',
        'ip',
        'badge',
    ];
}
