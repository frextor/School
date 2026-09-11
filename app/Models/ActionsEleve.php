<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_actions_eleve`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ActionsEleve extends Model
{
    protected $table = 'amos_actions_eleve';
    protected $primaryKey = 'id_action';
    public $timestamps = false;

    protected $fillable = [
        'engagement_associatif',
        'projet_tutore',
        'participation_salon',
        'stage_sejour',
        'commentaire',
        'id_eleve',
    ];
}
