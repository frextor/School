<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_semestre`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Semestre extends Model
{
    protected $table = 'amos_semestre';
    protected $primaryKey = 'id_semestre';
    public $timestamps = false;

    protected $fillable = [
        'libelle_semestre',
    ];
}
