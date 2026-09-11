<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_config_pdf`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielConfigPdf extends Model
{
    protected $table = 'amos_referentiel_config_pdf';
    protected $primaryKey = 'id_referentiel_config_pdf';
    public $timestamps = false;

    protected $fillable = [
        'logo',
        'texte_attestation',
        'texte_facture',
        'texte_facture_contrat_apprentissage',
        'texte_facture_contrat_pro',
        'footer_initial',
        'footer_apprentissage',
        'footer_professionnalisation',
        'type',
    ];
}
