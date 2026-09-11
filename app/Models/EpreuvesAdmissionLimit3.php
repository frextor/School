<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_epreuves_admission_limit3`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EpreuvesAdmissionLimit3 extends Model
{
    protected $table = 'amos_epreuves_admission_limit3';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_epreuve_admission',
        'date_epreuve',
        'lieu',
        'effectif',
    ];
}
