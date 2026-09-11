<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_files_activite_intervenant`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class FilesActiviteIntervenant extends Model
{
    protected $table = 'amos_files_activite_intervenant';
    protected $primaryKey = 'id_file_activite';
    public $timestamps = false;

    protected $fillable = [
        'id_activite_intervenant',
        'fichier',
    ];
}
