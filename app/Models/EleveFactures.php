<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_factures`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveFactures extends Model
{
    protected $table = 'amos_eleve_factures';
    protected $primaryKey = 'id_facture';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve_parent',
        'reference',
        'id_eleve',
        'date',
        'id_contrat',
        'info_supplementaire',
        'periode_formation',
        'nb_heure_periode_formation',
        'nb_heures_absences_injustifiees',
        'id_payeur',
        'contrat_pro_option',
        'new_montant_formation',
        'montant_formation_du',
    ];
}
