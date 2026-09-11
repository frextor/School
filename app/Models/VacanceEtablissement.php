<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_vacance_etablissement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class VacanceEtablissement extends Model
{
    protected $table = 'amos_vacance_etablissement';
    protected $primaryKey = 'id_vacance_etablissement';
    public $timestamps = false;

    protected $fillable = [
        'id_referentiel_vacance',
        'id_etablissement',
    ];
}
