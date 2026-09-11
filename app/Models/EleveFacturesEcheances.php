<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_factures_echeances`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveFacturesEcheances extends Model
{
    protected $table = 'amos_eleve_factures_echeances';
    protected $primaryKey = 'id_facture_echeance';
    public $timestamps = false;

    protected $fillable = [
        'id_facture',
        'id_cheque_paiement',
    ];
}
