<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_absence_eleve`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class AbsenceEleve extends Model
{
    protected $table = 'amos_absence_eleve';
    protected $primaryKey = 'id_absence';
    public $timestamps = false;

    protected $fillable = [
        'date_absence',
        'heure_absence',
        'id_cours',
        'retards_non_justifies',
        'retards_excuses',
        'absences_non_justifies',
        'absences_excuses',
        'id_eleve',
        'id_unite_enseignement',
        'annotation',
        'semestre',
        'valide',
        'justificatif',
        'justificatif_fichers',
        'date_justificatif',
        'modification_justificatif',
    ];
}
