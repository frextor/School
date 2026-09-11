<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_disponibilite_intervenants`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class DisponibiliteIntervenants extends Model
{
    protected $table = 'amos_disponibilite_intervenants';
    protected $primaryKey = 'id_disponibilite_intervenants';
    public $timestamps = false;

    protected $fillable = [
        'id_intervenant',
        'id_etablissement',
        'date_debut',
        'date_fin',
    ];
}
