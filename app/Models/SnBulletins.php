<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_sn_bulletins`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class SnBulletins extends Model
{
    protected $table = 'amos_sn_bulletins';
    protected $primaryKey = 'id_bulletin';
    public $timestamps = false;

    protected $fillable = [
        'pdf',
        'annee',
        'semestre',
        'id_etablissement',
        'id_eleve',
        'id_niveau',
        'session',
        'date_insert',
        'date_update',
        'active',
    ];
}
