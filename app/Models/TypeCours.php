<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCours extends Model
{
    protected $table = 'amos_type_cours';
    protected $primaryKey = 'id_type_cours';
    public $timestamps = false;

    protected $fillable = ['type_cours'];
}
