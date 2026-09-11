<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_avoirs`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveAvoirs extends Model
{
    protected $table = 'amos_eleve_avoirs';
    protected $primaryKey = 'id_avoir';
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
        'annee_rentree',
        'contrat_pro_option',
        'montant_formation_du',
    ];
}
