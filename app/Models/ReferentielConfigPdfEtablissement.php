<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_config_pdf_etablissement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielConfigPdfEtablissement extends Model
{
    protected $table = 'amos_referentiel_config_pdf_etablissement';
    protected $primaryKey = 'id_referentiel_config_pdf_etablissement';
    public $timestamps = false;

    protected $fillable = [
        'id_referentiel_config_pdf',
        'id_etablissement',
    ];
}
