<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de la "saisie" de paiement élève (`Eleves.php::add_reglements()`,
 * table `amos_paiement_eleve`) — plan de paiement/contrat associé à une
 * inscription (année/niveau), auquel se rattachent des chèques
 * (`amos_cheques_paiement`) et des options facturées
 * (`amos_eleve_paiements_options`).
 */
class PaiementEleve extends Model
{
    protected $table = 'amos_paiement_eleve';
    protected $primaryKey = 'id_paiement_eleve';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve', 'id_eleve_parent', 'titre', 'date', 'date_modification',
        'commentaire', 'id_etablissement', 'id_contrat', 'id_niveau',
        'annee_rentree', 'accord_opco', 'mode_paiement', 'paiement_recu',
        'type_saisie_apprentissage',
    ];

    protected $casts = [
        'date' => 'datetime',
        'date_modification' => 'datetime',
        'accord_opco' => 'boolean',
        'paiement_recu' => 'boolean',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    public function cheques()
    {
        return $this->hasMany(ChequePaiement::class, 'id_paiement_eleve', 'id_paiement_eleve');
    }

    public function options()
    {
        return $this->hasMany(PaiementEleveOption::class, 'id_paiement_eleve', 'id_paiement_eleve');
    }

    public function montantTotal(): float
    {
        return (float) $this->cheques()->sum('montant_paiement');
    }
}
