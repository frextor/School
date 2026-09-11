<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_secteurs_activites`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesSecteursActivites extends Model
{
    protected $table = 'amos_entreprises_secteurs_activites';
    protected $primaryKey = 'id_entreprises_secteurs_activites';
    public $timestamps = false;

    protected $fillable = [
        'nom_secteur',
    ];
}
