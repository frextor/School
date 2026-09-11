<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_familles_des_sources`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class FamillesDesSources extends Model
{
    protected $table = 'amos_familles_des_sources';
    protected $primaryKey = 'id_famille';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
    ];
}
