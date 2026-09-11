<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_vacance_classe`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class VacanceClasse extends Model
{
    protected $table = 'amos_vacance_classe';
    protected $primaryKey = 'id_vacance_classe';
    public $timestamps = false;

    protected $fillable = [
        'id_referentiel_vacance',
        'id_classe',
    ];
}
