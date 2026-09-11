<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_contact_ecoles`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ContactEcoles extends Model
{
    protected $table = 'amos_contact_ecoles';
    protected $primaryKey = 'id_contact_ecole';
    public $timestamps = false;

    protected $fillable = [
        'id_contact',
        'etablissement',
        'ordre',
    ];
}
