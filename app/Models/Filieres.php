<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_filieres`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Filieres extends Model
{
    protected $table = 'amos_filieres';
    protected $primaryKey = 'id_filiere';
    public $timestamps = false;

    protected $fillable = [
        'libelle',
    ];
}
