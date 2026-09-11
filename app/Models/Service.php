<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'amos_services';
    protected $primaryKey = 'id_service';

    protected $fillable = ['libelle', 'id_parent'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'id_parent', 'id_service');
    }

    public function enfants()
    {
        return $this->hasMany(self::class, 'id_parent', 'id_service');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'id_service', 'id_service');
    }
}
