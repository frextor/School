<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_diplomes`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Diplomes extends Model
{
    protected $table = 'amos_diplomes';
    protected $primaryKey = 'id_diplome';
    public $timestamps = false;

    protected $fillable = [
        'nom_diplome',
    ];
}
