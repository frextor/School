<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_eleves_pieces`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesElevesPieces extends Model
{
    protected $table = 'amos_entreprises_eleves_pieces';
    protected $primaryKey = 'id_piece';
    public $timestamps = false;

    protected $fillable = [
        'id_entreprises_eleves',
        'id_type_piece',
        'path',
        'file',
        'periode',
    ];
}
