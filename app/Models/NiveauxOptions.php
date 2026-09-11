<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_niveaux_options`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class NiveauxOptions extends Model
{
    protected $table = 'amos_niveaux_options';
    protected $primaryKey = 'id_niveau_option';
    public $timestamps = false;

    protected $fillable = [
        'id_niveau',
        'id_objet_paiement',
        'titre',
        'montant',
        'ordre',
        'annee',
    ];
}
