<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_db_migrations`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class DbMigrations extends Model
{
    protected $table = 'amos_db_migrations';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'version',
    ];
}
