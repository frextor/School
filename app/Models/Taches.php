<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_taches`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Taches extends Model
{
    protected $table = 'amos_taches';
    protected $primaryKey = 'id_tache';
    public $timestamps = true;

    protected $fillable = [
        'id_contact',
        'id_admin_assigne',
        'id_service_assigne',
        'id_ecole_assigne',
        'type_tache',
        'objet',
        'commentaire',
        'date_debut',
        'deadline',
        'date_realisation',
        'score',
        'id_type_tache',
        'id_statut_contact',
        'id_statut_tache',
        'archive',
    ];
}
