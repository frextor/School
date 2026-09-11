<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_factures_items`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveFacturesItems extends Model
{
    protected $table = 'amos_eleve_factures_items';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_facture',
        'id_objet_paiement',
        'titre_objet_paiement',
        'id_eleve',
        'id_niveau',
        'montant',
    ];
}
