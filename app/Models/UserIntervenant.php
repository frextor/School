<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Portage de `Login_users_intervenants_model` (table `amos_users_intervenant`)
 * — compte de connexion "espace intervenant", distinct du compte admin.
 * Mot de passe legacy en md5 sans salt : voir `checkPassword()`.
 */
class UserIntervenant extends Authenticatable
{
    protected $table = 'amos_users_intervenant';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $hidden = ['password', 'token'];

    protected $fillable = ['id_intervenant', 'username', 'password', 'connexion', 'token', 'valide'];

    protected $casts = [
        'valide' => 'boolean',
        'connexion' => 'datetime',
    ];

    public function intervenant()
    {
        return $this->belongsTo(Intervenant::class, 'id_intervenant', 'id_intervenant');
    }

    public function checkPassword(string $plainPassword): bool
    {
        return hash_equals($this->password, md5($plainPassword));
    }

    public function setPasswordFromPlain(string $plainPassword): void
    {
        $this->password = md5($plainPassword);
    }
}
