<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_absence_historique_courriel`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveAbsenceHistoriqueCourriel extends Model
{
    protected $table = 'amos_eleve_absence_historique_courriel';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'modele',
        'date',
        'id_eleve',
        'fait',
        'link',
        'semestre',
        'id_directeur',
        'date_reunion',
        'time_reunion',
        'adresse',
        'entretien',
    ];
}
