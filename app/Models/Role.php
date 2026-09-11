<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'amos_roles';
    protected $primaryKey = 'id_role';
    public $timestamps = false;

    protected $fillable = ['nom_role', 'nom_machine', 'date_creation', 'date_modification', 'locked'];

    protected $casts = [
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
        'locked' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'amos_roles_permissions',
            'id_role',
            'id_permission'
        );
    }

    /** Les admins sont rattachés par le champ texte `profil` = `nom_machine` du rôle (legacy). */
    public function admins()
    {
        return $this->hasMany(Admin::class, 'profil', 'nom_machine');
    }
}
