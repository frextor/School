<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntervenantSecteurActivite extends Model
{
    protected $table = 'amos_intervenant_secteur_activite';
    protected $primaryKey = 'id_secteur_activite';
    public $timestamps = false;

    protected $fillable = ['nom_secteur_activite', 'id_intervenant'];
}
