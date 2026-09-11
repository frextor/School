<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_contacts`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesContacts extends Model
{
    protected $table = 'amos_entreprises_contacts';
    protected $primaryKey = 'id_entreprises_contacts';
    public $timestamps = false;

    protected $fillable = [
        'id_entreprise',
        'civilite',
        'nom',
        'prenom',
        'telephone',
        'tel_country_contact',
        'code_country_contact',
        'email',
        'id_poste',
        'id_type_contact',
        'commentaire',
        'mautic_id',
        'date_de_creation',
    ];
}
