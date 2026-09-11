<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_module_type_cours`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ModuleTypeCours extends Model
{
    protected $table = 'amos_module_type_cours';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_unite_enseignement',
        'id_type_cours',
        'value_horaire',
    ];
}
