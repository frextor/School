<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_formations`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveFormations extends Model
{
    protected $table = 'amos_eleve_formations';
    protected $primaryKey = 'id_eleve_formation';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'intitule_formation',
        'lieu_formation',
        'date_formation',
        'niveau_formation',
        'diplome_formation',
    ];
}
