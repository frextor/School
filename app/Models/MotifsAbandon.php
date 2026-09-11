<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_motifs_abandon`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class MotifsAbandon extends Model
{
    protected $table = 'amos_motifs_abandon';
    protected $primaryKey = 'id_motif';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
    ];
}
