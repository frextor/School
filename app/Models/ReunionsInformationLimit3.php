<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_reunions_information_limit3`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReunionsInformationLimit3 extends Model
{
    protected $table = 'amos_reunions_information_limit3';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_reunion_information',
        'date',
        'lieu',
        'effectif',
    ];
}
