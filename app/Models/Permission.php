<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'amos_permissions';
    protected $primaryKey = 'id_permission';
    public $timestamps = false;

    protected $fillable = ['nom_permission', 'route', 'date_creation', 'date_modification', 'ParentID'];

    protected $casts = [
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'amos_roles_permissions',
            'id_permission',
            'id_role'
        );
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'ParentID', 'id_permission');
    }

    public function enfants()
    {
        return $this->hasMany(self::class, 'ParentID', 'id_permission');
    }
}
