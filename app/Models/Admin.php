<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Portage du modèle `admins` / `login_model` CodeIgniter.
 *
 * Le mot de passe legacy est stocké en `md5(password)` (pas de salt, pas de
 * bcrypt). On ne peut donc pas utiliser le hasher par défaut de Laravel :
 * la vérification se fait "à la main" dans AuthController::login() via
 * Admin::checkPassword(). Le modèle reste Authenticatable pour pouvoir
 * s'appuyer sur Auth::guard('admin')->login()/logout()/user().
 */
class Admin extends Authenticatable
{
    protected $table = 'amos_admins';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    protected $hidden = ['password', 'token'];

    protected $fillable = [
        'nom', 'prenom', 'username', 'password', 'email', 'profil', 'avatar', 'id_service', 'token',
    ];

    /** Établissements rattachés à cet admin (table pivot amos_admins_etablissement). */
    public function etablissements()
    {
        return $this->belongsToMany(
            Etablissement::class,
            'amos_admins_etablissement',
            'id_admin',
            'id_etablissement'
        )->withPivot('etablissement_principal');
    }

    public function etablissementPrincipal()
    {
        return $this->etablissements()->wherePivot('etablissement_principal', 1);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    /** Le rôle est référencé par le champ texte `profil` = `nom_machine` du rôle (legacy). */
    public function role()
    {
        return $this->belongsTo(Role::class, 'profil', 'nom_machine');
    }

    /** Équivalent de `md5($password) === $this->password` côté legacy. */
    public function checkPassword(string $plainPassword): bool
    {
        return hash_equals($this->password, md5($plainPassword));
    }

    public function setPasswordFromPlain(string $plainPassword): void
    {
        $this->password = md5($plainPassword);
    }
}
