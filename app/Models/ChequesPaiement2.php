<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_cheques_paiement2`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ChequesPaiement2 extends Model
{
    protected $table = 'amos_cheques_paiement2';
    protected $primaryKey = 'id_cheque_paiement';
    public $timestamps = false;

    protected $fillable = [
        'photo_cheque',
        'numero_cheque',
        'montant_paiement',
        'date_encaissement',
        'nom_banque',
        'id_paiement_eleve',
        'id_objet_paiement',
        'date_echeance',
    ];
}
