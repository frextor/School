<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_infos_parents`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveInfosParents extends Model
{
    protected $table = 'amos_eleve_infos_parents';
    protected $primaryKey = 'id_infos_parents';
    public $timestamps = false;

    protected $fillable = [
        'nom_parent',
        'prenom_parent',
        'email_parent',
        'telephone_personnel_parent',
        'telephone_pro_parent',
        'profession_parent',
        'nom_entreprise_parent',
        'code_postal_parent',
        'ville_parent',
        'pays_parent',
        'adresse_parent',
        'lien_parente',
        'position_formulaire',
        'id_famille_des_sources',
        'id_source',
        'autres',
        'id_eleve',
    ];
}
