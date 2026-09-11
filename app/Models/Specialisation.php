<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialisation extends Model
{
    protected $table = 'amos_specialisation';
    protected $primaryKey = 'id_specialisation';
    public $timestamps = false;

    protected $fillable = ['nom_specialisation', 'date_creation', 'date_modification'];

    protected $casts = [
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];
}
