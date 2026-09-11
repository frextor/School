<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_factures_old`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveFacturesOld extends Model
{
    protected $table = 'amos_eleve_factures_old';
    protected $primaryKey = 'id_facture';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve_parent',
        'reference',
        'id_eleve',
        'date',
    ];
}
