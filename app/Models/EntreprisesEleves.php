<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_eleves`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesEleves extends Model
{
    protected $table = 'amos_entreprises_eleves';
    protected $primaryKey = 'id_entreprises_eleves';
    public $timestamps = false;

    protected $fillable = [
        'id_entreprise',
        'id_eleve',
        'id_eleve_parent',
        'annee_scolaire',
        'id_entreprises_type_contrat',
        'taux_horaire_formation',
        'montant_formation',
        'id_tuteur',
        'id_departement',
        'id_poste',
        'id_opco',
        'num_dossier',
        'montant_formation_opco',
        'taux_horaire_opco',
        'cout_branche',
        'id_contact_opco',
        'accord_opco',
        'contrat_signe',
        'info_supplementaire',
        'periode_contrat',
        'periode_formation',
        'periode_formation_annuelle',
        'nb_heure_periode_formation_annuelle',
    ];
}
