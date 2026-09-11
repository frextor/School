<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_classe_annees`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ClasseAnnees extends Model
{
    protected $table = 'amos_classe_annees';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_classe',
        'annee',
    ];
}
