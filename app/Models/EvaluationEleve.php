<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_evaluation_eleve`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EvaluationEleve extends Model
{
    protected $table = 'amos_evaluation_eleve';
    protected $primaryKey = 'id_evaluation';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'note',
        'id_eleve',
        'id_etablissement',
        'id_niveau',
        'id_classe',
        'id_matiere',
        'id_cours',
        'coefficient',
        'type_evaluation',
        'semestre',
        'commentaire_evaluation',
    ];
}
