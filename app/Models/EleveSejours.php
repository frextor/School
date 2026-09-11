<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_sejours`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveSejours extends Model
{
    protected $table = 'amos_eleve_sejours';
    protected $primaryKey = 'id_eleve_sejour';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'pays_sejour',
        'type_sejour',
        'titule_etudes',
    ];
}
