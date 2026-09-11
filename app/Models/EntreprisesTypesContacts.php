<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_types_contacts`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesTypesContacts extends Model
{
    protected $table = 'amos_entreprises_types_contacts';
    protected $primaryKey = 'id_type_contact';
    public $timestamps = false;

    protected $fillable = [
        'libelle',
    ];
}
