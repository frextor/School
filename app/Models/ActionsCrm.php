<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_actions_crm`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ActionsCrm extends Model
{
    protected $table = 'amos_actions_crm';
    protected $primaryKey = 'id_action';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
        'info_utile',
        'actif',
    ];
}
