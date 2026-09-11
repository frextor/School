<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_sn_evaluations_existantes`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class SnEvaluationsExistantes extends Model
{
    protected $table = 'amos_sn_evaluations_existantes';
    protected $primaryKey = 'id_evaluation';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'annee',
        'semestre',
        'id_referentiel',
        'id_ue',
        'id_matiere',
        'id_type_evaluation',
        'nom_evaluation',
        'date_evaluation',
        'heure_debut',
        'heure_fin',
        'type_notation',
        'boolean_facultatif',
        'referentiel',
        'id_evaluation_parent',
    ];
}
