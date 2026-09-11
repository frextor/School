<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_sync_mautic`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class SyncMautic extends Model
{
    protected $table = 'amos_sync_mautic';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_contact',
        'date',
    ];
}
