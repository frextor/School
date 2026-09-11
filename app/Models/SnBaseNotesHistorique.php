<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_sn_base_notes_historique`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class SnBaseNotesHistorique extends Model
{
    protected $table = 'amos_sn_base_notes_historique';
    protected $primaryKey = 'id_sn_base_notes_historique';
    public $timestamps = false;

    protected $fillable = [
        'id_note',
        'ancienne_note',
        'nouvelle_note',
        'raison',
        'session',
        'id_admin',
        'date',
    ];
}
