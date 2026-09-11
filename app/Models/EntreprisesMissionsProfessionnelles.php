<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_missions_professionnelles`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesMissionsProfessionnelles extends Model
{
    protected $table = 'amos_entreprises_missions_professionnelles';
    protected $primaryKey = 'id_entreprises_missions_professionnelles';
    public $timestamps = false;

    protected $fillable = [
        'id_entreprise',
        'id_eleve',
        'id_classe',
        'annee',
        'id_type_mission',
        'id_departement',
        'id_poste',
        'duree_reelle',
        'duree_academique',
        'credits',
        'ects_finaux',
        'information_complementaire',
        'files_mission',
        'date_debut',
        'date_fin',
    ];
}
