<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_experiances`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveExperiances extends Model
{
    protected $table = 'amos_eleve_experiances';
    protected $primaryKey = 'id_eleve_experiance';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'entreprise',
        'date_debut',
        'date_fin',
        'post_occupe',
        'principales_missions',
        'principales_responsabilites',
    ];
}
