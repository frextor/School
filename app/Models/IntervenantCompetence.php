<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntervenantCompetence extends Model
{
    protected $table = 'amos_intervenant_competences';
    protected $primaryKey = 'id_competence';
    public $timestamps = false;

    protected $fillable = ['nom_competence', 'id_intervenant'];
}
