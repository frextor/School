<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_epreuves_admission`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EpreuvesAdmission extends Model
{
    protected $table = 'amos_epreuves_admission';
    protected $primaryKey = 'id_epreuve_admission';
    public $timestamps = false;

    protected $fillable = [
        'date_epreuve',
        'lieu',
        'effectif',
        'distanciel',
        'url_distanciel',
    ];
}
