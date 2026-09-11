<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_regles`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielRegles extends Model
{
    protected $table = 'amos_referentiel_regles';
    protected $primaryKey = 'id_regle';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'annee',
        'semestre',
        'niveau',
        'id_classe',
        'id_ue',
        'id_matiere',
        'code_regle',
        'is_actif',
    ];
}
