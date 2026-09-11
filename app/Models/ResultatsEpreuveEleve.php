<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_resultats_epreuve_eleve`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ResultatsEpreuveEleve extends Model
{
    protected $table = 'amos_resultats_epreuve_eleve';
    protected $primaryKey = 'id_resultat_epreuve_eleve';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'id_epreuve_admission',
        'anglais',
        'culture_generale',
        'epreuve_redaction',
        'entretien',
        'decision',
        'id_motif_refus',
        'archive',
        'date_operation',
    ];
}
