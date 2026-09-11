<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Portage de `Login_users_model` (table `amos_users`) — compte de connexion
 * "espace élève", guard distinct de admin/intervenant/entreprise.
 * Mot de passe legacy en md5 sans salt.
 *
 * NON couvert : le cas particulier `password == '5bc8b974e58bf0e8c63712c1a6ddf2cd'
 * && connexion == '0000-00-00 00:00:00'` (mot de passe temporaire imposant une
 * réinitialisation à la première connexion) — dépend de l'envoi d'email
 * (`send_token`), non migré.
 */
class UserEleve extends Authenticatable
{
    protected $table = 'amos_users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $hidden = ['password', 'token'];

    protected $fillable = ['id_eleve', 'username', 'password', 'connexion', 'token', 'valide', 'email_etudiant'];

    protected $casts = [
        'valide' => 'boolean',
        'connexion' => 'datetime',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function checkPassword(string $plainPassword): bool
    {
        return hash_equals($this->password, md5($plainPassword));
    }
}
