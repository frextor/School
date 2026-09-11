<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `Configuration::site()` / `get_type_access()` (table `amos_type_access`) — activation de sections du site. */
class TypeAccess extends Model
{
    protected $table = 'amos_type_access';
    protected $primaryKey = 'id_access';
    public $timestamps = false;

    protected $fillable = ['id_parent', 'class_method', 'method', 'status', 'libelle'];

    protected $casts = ['status' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'id_parent', 'id_access');
    }

    public function enfants()
    {
        return $this->hasMany(self::class, 'id_parent', 'id_access');
    }
}
