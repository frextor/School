<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_eleves_types_piece`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesElevesTypesPiece extends Model
{
    protected $table = 'amos_entreprises_eleves_types_piece';
    protected $primaryKey = 'id_type_piece';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'periode_associee',
    ];
}
