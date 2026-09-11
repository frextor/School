<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_niveaux_langues`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveNiveauxLangues extends Model
{
    protected $table = 'amos_eleve_niveaux_langues';
    protected $primaryKey = 'id_eleve_niveaux_langues';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'id_niveau_langue',
    ];
}
