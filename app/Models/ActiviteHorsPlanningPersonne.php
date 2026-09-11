<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_activite_hors_planning_personne`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ActiviteHorsPlanningPersonne extends Model
{
    protected $table = 'amos_activite_hors_planning_personne';
    protected $primaryKey = 'id_activite_hors_planning_personne';
    public $timestamps = false;

    protected $fillable = [
        'id_personne',
        'id_activite_hors_planning',
        'date_debut',
        'date_fin',
    ];
}
