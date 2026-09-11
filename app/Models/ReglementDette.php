<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_reglement_dette`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReglementDette extends Model
{
    protected $table = 'amos_reglement_dette';
    protected $primaryKey = 'id_reglement_dette';
    public $timestamps = false;

    protected $fillable = [
        'date_reglement',
        'montant_reglement',
        'numero_cheque',
        'id_dette',
    ];
}
