<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_langues`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveLangues extends Model
{
    protected $table = 'amos_eleve_langues';
    protected $primaryKey = 'id_eleve_langue';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'langue',
        'nb_annee_etudes_langue',
        'parle_langue',
        'lue_langue',
        'ecrite_langue',
        'diplome_langue',
    ];
}
