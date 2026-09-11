<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_epreuves_admission_eleves`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EpreuvesAdmissionEleves extends Model
{
    protected $table = 'amos_epreuves_admission_eleves';
    protected $primaryKey = 'id_epreuve_admission_eleve';
    public $timestamps = false;

    protected $fillable = [
        'id_epreuve_admission',
        'id_eleve',
        'presence',
        'date_operation',
    ];
}
