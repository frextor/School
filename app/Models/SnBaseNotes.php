<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_sn_base_notes`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class SnBaseNotes extends Model
{
    protected $table = 'amos_sn_base_notes';
    protected $primaryKey = 'id_note';
    public $timestamps = false;

    protected $fillable = [
        'id_campus',
        'annee',
        'semestre',
        'id_referentiel',
        'id_ue',
        'id_matiere',
        'id_type',
        'id_evaluation',
        'eval_session',
        'id_eleve',
        'validation_sans_note',
        'note',
        'publier_admin',
        'publier_eleve',
        'date_saisie',
        'session',
        'referentiel',
    ];
}
