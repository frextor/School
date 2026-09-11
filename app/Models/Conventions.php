<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_conventions`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Conventions extends Model
{
    protected $table = 'amos_conventions';
    protected $primaryKey = 'id_convention';
    public $timestamps = false;

    protected $fillable = [
        'titre_document',
        'nom_document',
        'id_niveau',
        'id_etablissement',
        'date_creation',
        'date_modification',
    ];
}
