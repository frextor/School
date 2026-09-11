<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntervenantDiplome extends Model
{
    protected $table = 'amos_intervenant_diplomes';
    protected $primaryKey = 'id_diplome';
    public $timestamps = false;

    protected $fillable = ['titre_diplome', 'id_intervenant'];
}
