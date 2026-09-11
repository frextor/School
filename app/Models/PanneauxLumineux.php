<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_panneaux_lumineux`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class PanneauxLumineux extends Model
{
    protected $table = 'amos_panneaux_lumineux';
    protected $primaryKey = 'id_panneau';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'annee',
        'identifiant_panneaux',
        'titre',
        'plage_horaire',
        'delai_horaire',
    ];
}
