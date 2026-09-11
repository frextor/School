<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_batch_queue`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class BatchQueue extends Model
{
    protected $table = 'amos_batch_queue';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'data',
    ];
}
