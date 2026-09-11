<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_add_rows`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class AddRows extends Model
{
    protected $table = 'amos_add_rows';
    protected $primaryKey = 'id_add_rows';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'date_add',
        'nb_add',
    ];
}
