<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_livret`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveLivret extends Model
{
    protected $table = 'amos_eleve_livret';
    protected $primaryKey = 'id_livret';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'id_etablissement',
        'annee',
        'id_classe',
        'semestre',
        'session',
        'status',
        'creation',
    ];
}
