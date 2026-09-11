<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_users`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class Users extends Model
{
    protected $table = 'amos_users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'id_eleve',
        'username',
        'password',
        'connexion',
        'token',
        'valide',
        'email_etudiant',
    ];
}
