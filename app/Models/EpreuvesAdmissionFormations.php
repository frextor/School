<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_epreuves_admission_formations`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EpreuvesAdmissionFormations extends Model
{
    protected $table = 'amos_epreuves_admission_formations';
    protected $primaryKey = 'id__epreuves_admission_formation';
    public $timestamps = false;

    protected $fillable = [
        'id_epreuve_admission',
        'id_formation',
    ];
}
