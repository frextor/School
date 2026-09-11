<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_types_contrat`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesTypesContrat extends Model
{
    protected $table = 'amos_entreprises_types_contrat';
    protected $primaryKey = 'id_entreprises_type_contrat';
    public $timestamps = false;

    protected $fillable = [
        'type_contrat',
    ];
}
