<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_niveaux_langues`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class NiveauxLangues extends Model
{
    protected $table = 'amos_niveaux_langues';
    protected $primaryKey = 'id_niveau_langue';
    public $timestamps = false;

    protected $fillable = [
        'titre',
        'montant',
    ];
}
