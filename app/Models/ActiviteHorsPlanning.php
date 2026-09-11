<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_activite_hors_planning`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ActiviteHorsPlanning extends Model
{
    protected $table = 'amos_activite_hors_planning';
    protected $primaryKey = 'id_activite_hors_planning';
    public $timestamps = false;

    protected $fillable = [
        'activite_hors_planning_nom',
        'activite_hors_planning_couleur',
    ];
}
