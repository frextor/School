<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_bulletin`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveBulletin extends Model
{
    protected $table = 'amos_eleve_bulletin';
    protected $primaryKey = 'id_bls';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'id_etablissement',
        'annee',
        'id_referentiel',
        'semestre',
        'session',
        'commentaire',
        'decision_jury',
        'bulletin_json',
        'est_publie',
        'date_create',
    ];
}
