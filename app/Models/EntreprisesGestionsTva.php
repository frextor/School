<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_entreprises_gestions_tva`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EntreprisesGestionsTva extends Model
{
    protected $table = 'amos_entreprises_gestions_tva';
    protected $primaryKey = 'id_entreprises_gestion_tva';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'gestion',
        'taux',
    ];
}
