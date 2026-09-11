<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_signatures`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielSignatures extends Model
{
    protected $table = 'amos_referentiel_signatures';
    protected $primaryKey = 'id_signature';
    public $timestamps = false;

    protected $fillable = [
        'civilite',
        'nom_directeur',
        'id_etablissement',
        'principal',
        'signature',
        'date_creation',
        'date_modification',
        'fonction',
    ];
}
