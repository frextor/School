<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Portage de `entreprises_portail_model` (table `amos_entreprises_portail`)
 * — compte de connexion "espace entreprise", guard distinct de admin/intervenant.
 * Mot de passe legacy en md5 sans salt.
 */
class EntreprisePortail extends Authenticatable
{
    protected $table = 'amos_entreprises_portail';
    protected $primaryKey = 'id_entreprises_portail';
    public $timestamps = false;

    protected $hidden = ['password', 'token'];

    protected $fillable = ['id_entreprise', 'username', 'password', 'connexion', 'token', 'valide'];

    protected $casts = [
        'valide' => 'boolean',
        'connexion' => 'datetime',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise', 'id_entreprise');
    }

    public function checkPassword(string $plainPassword): bool
    {
        return hash_equals($this->password, md5($plainPassword));
    }
}
