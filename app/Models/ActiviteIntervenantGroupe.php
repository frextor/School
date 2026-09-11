<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_activite_intervenant_groupe`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ActiviteIntervenantGroupe extends Model
{
    protected $table = 'amos_activite_intervenant_groupe';
    protected $primaryKey = 'id_activite_intervenant_groupe';
    public $timestamps = false;

    protected $fillable = [
        'id_activite_intervenant',
        'id_groupe',
    ];
}
