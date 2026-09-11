<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_types_taches`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class TypesTaches extends Model
{
    protected $table = 'amos_types_taches';
    protected $primaryKey = 'id_type_tache';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
    ];
}
