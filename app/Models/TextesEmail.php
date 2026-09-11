<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_textes_email`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class TextesEmail extends Model
{
    protected $table = 'amos_textes_email';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lang',
        'categorie',
        'sujet',
        'message',
        'statut',
        'titre',
    ];
}
