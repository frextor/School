<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_langues`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Langues extends Model
{
    protected $table = 'amos_langues';
    protected $primaryKey = 'id_langue';
    public $timestamps = false;

    protected $fillable = [
        'id_langue',
        'en',
        'fr',
    ];
}
