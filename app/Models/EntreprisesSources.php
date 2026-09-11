<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_sources`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesSources extends Model
{
    protected $table = 'amos_entreprises_sources';
    protected $primaryKey = 'id_entreprises_source';
    public $timestamps = false;

    protected $fillable = [
        'titre',
        'id_entreprises_famille',
    ];
}
