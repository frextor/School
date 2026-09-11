<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_clients`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Clients extends Model
{
    protected $table = 'amos_clients';
    protected $primaryKey = 'numero_client';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
    ];
}
