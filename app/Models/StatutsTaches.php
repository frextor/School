<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_statuts_taches`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class StatutsTaches extends Model
{
    protected $table = 'amos_statuts_taches';
    protected $primaryKey = 'id_statut_tache';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
    ];
}
