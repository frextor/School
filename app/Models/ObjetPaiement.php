<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_objet_paiement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ObjetPaiement extends Model
{
    protected $table = 'amos_objet_paiement';
    protected $primaryKey = 'id_objet_paiement';
    public $timestamps = false;

    protected $fillable = [
        'objet_paiement',
        'reference',
        'type',
    ];
}
