<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_salles`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Salles extends Model
{
    protected $table = 'amos_salles';
    protected $primaryKey = 'id_salle';
    public $timestamps = false;

    protected $fillable = [
        'code_salle',
        'nom_salle',
        'nombre_place',
        'id_etablissement',
        'date_creation',
        'ip',
    ];
}
