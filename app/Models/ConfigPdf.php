<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `etablissement_model->fetch_config_pdf/add_config_attestation`,
 * table `amos_referentiel_config_pdf`. Une même table sert aux 3 variantes
 * legacy (factures, avoirs, attestations) via la colonne `type`
 * (varchar(11) : 'facture' | 'avoir' | 'attestation').
 */
class ConfigPdf extends Model
{
    protected $table = 'amos_referentiel_config_pdf';
    protected $primaryKey = 'id_referentiel_config_pdf';
    public $timestamps = false;

    public const TYPE_FACTURE = 'facture';
    public const TYPE_AVOIR = 'avoir';
    public const TYPE_ATTESTATION = 'attestation';

    protected $fillable = [
        'logo', 'texte_attestation', 'texte_facture', 'texte_facture_contrat_apprentissage',
        'texte_facture_contrat_pro', 'footer_initial', 'footer_apprentissage',
        'footer_professionnalisation', 'type',
    ];

    public function etablissements()
    {
        return $this->belongsToMany(
            Etablissement::class,
            'amos_referentiel_config_pdf_etablissement',
            'id_referentiel_config_pdf',
            'id_etablissement'
        );
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
