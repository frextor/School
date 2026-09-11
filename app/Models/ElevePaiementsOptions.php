<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_paiements_options`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ElevePaiementsOptions extends Model
{
    protected $table = 'amos_eleve_paiements_options';
    protected $primaryKey = 'id_eleve_paiements_options';
    public $timestamps = false;

    protected $fillable = [
        'id_paiement_eleve',
        'id_eleve',
        'id_niveau_option',
        'montant',
    ];
}
