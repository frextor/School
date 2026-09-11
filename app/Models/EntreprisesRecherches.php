<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_recherches`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesRecherches extends Model
{
    protected $table = 'amos_entreprises_recherches';
    protected $primaryKey = 'id_entreprises_recherche';
    public $timestamps = false;

    protected $fillable = [
        'nom_recherche',
    ];
}
