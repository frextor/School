<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_motifs_refus_candidat`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class MotifsRefusCandidat extends Model
{
    protected $table = 'amos_motifs_refus_candidat';
    protected $primaryKey = 'id_motif_refus';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
    ];
}
