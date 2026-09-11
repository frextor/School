<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_contact_origine_traces`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ContactOrigineTraces extends Model
{
    protected $table = 'amos_contact_origine_traces';
    protected $primaryKey = 'id_contact_origine_trace';
    public $timestamps = false;

    protected $fillable = [
        'id_contact',
        'id_contact_parent',
        'trace',
        'espace',
        'date',
        'utilisateur',
        'id_tache',
        'abouti',
        'entrant',
        'id_action',
        'id_canal',
        'id_utilisateur',
        'type_tache',
        'id_motif_abandon',
        'type_utilisateur',
    ];
}
