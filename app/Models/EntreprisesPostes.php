<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_postes`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesPostes extends Model
{
    protected $table = 'amos_entreprises_postes';
    protected $primaryKey = 'id_entreprises_postes';
    public $timestamps = false;

    protected $fillable = [
        'nom_poste',
    ];
}
