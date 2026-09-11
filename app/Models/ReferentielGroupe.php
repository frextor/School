<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_groupe`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielGroupe extends Model
{
    protected $table = 'amos_referentiel_groupe';
    protected $primaryKey = 'id_referentiel_groupe';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'id_unite_enseignement',
        'anne',
        'id_groupe',
        'semestre',
        'id_cours',
        'cc',
        'cr',
        'td',
        'ei',
        'cc_val',
        'cr_val',
        'td_val',
        'ei_val',
        'id_intervenant',
        'volume',
        'ects',
        'id_classe',
        'modifie',
        'ignore',
    ];
}
