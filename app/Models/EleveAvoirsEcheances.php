<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_avoirs_echeances`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveAvoirsEcheances extends Model
{
    protected $table = 'amos_eleve_avoirs_echeances';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_avoir',
        'id_cheque_paiement',
        'numero_echeance',
        'echeance',
        'date_echeance',
        'montant_a_verser',
        'id_eleve',
        'id_niveau',
    ];
}
