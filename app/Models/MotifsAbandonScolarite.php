<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_motifs_abandon_scolarite`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class MotifsAbandonScolarite extends Model
{
    protected $table = 'amos_motifs_abandon_scolarite';
    protected $primaryKey = 'id_motif_abandon_scolarite';
    public $timestamps = true;

    protected $fillable = [
        'libelle',
    ];
}
