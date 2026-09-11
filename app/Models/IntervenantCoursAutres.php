<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_intervenant_cours_autres`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class IntervenantCoursAutres extends Model
{
    protected $table = 'amos_intervenant_cours_autres';
    protected $primaryKey = 'id_intervenant_cours_autres';
    public $timestamps = false;

    protected $fillable = [
        'id_intervenant',
        'nom_cours',
    ];
}
