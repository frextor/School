<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_matiere_intervenant`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class MatiereIntervenant extends Model
{
    protected $table = 'amos_matiere_intervenant';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_matiere',
        'id_intervenant',
    ];
}
