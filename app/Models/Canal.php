<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_canal`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Canal extends Model
{
    protected $table = 'amos_canal';
    protected $primaryKey = 'id_canal';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
        'actif',
    ];
}
