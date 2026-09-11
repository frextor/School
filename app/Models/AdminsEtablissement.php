<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_admins_etablissement`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class AdminsEtablissement extends Model
{
    protected $table = 'amos_admins_etablissement';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_admin',
        'id_etablissement',
        'etablissement_principal',
    ];
}
