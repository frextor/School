<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_deplacements`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveDeplacements extends Model
{
    protected $table = 'amos_eleve_deplacements';
    protected $primaryKey = 'id_deplacement';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'semestre',
        'id_classe',
        'id_eleve',
        'date_aller',
        'date_retour',
        'statut',
    ];
}
