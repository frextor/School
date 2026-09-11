<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_taches`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesTaches extends Model
{
    protected $table = 'amos_entreprises_taches';
    protected $primaryKey = 'id_entreprises_taches';
    public $timestamps = true;

    protected $fillable = [
        'id_contact',
        'id_admin_assigne',
        'id_service_assigne',
        'id_entreprise_assigne',
        'valid',
        'type_tache',
        'objet',
        'commentaire',
        'telephone',
        'tel_country_contact',
        'score',
        'id_type_tache',
        'id_statut_contact',
        'id_statut_tache',
        'archive',
        'lieu',
        'deadline',
        'deadline2',
        'date_realisation',
    ];
}
