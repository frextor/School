<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_modes_paiement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ModesPaiement extends Model
{
    protected $table = 'amos_modes_paiement';
    protected $primaryKey = 'id_mode_paiement';
    public $timestamps = false;

    protected $fillable = [
        'libelle',
        'value',
        'system',
    ];
}
