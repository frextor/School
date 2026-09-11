<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_departements`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesDepartements extends Model
{
    protected $table = 'amos_entreprises_departements';
    protected $primaryKey = 'id_entreprises_departements';
    public $timestamps = false;

    protected $fillable = [
        'departement',
    ];
}
