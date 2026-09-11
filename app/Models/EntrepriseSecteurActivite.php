<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrepriseSecteurActivite extends Model
{
    protected $table = 'amos_entreprises_secteurs_activites';
    protected $primaryKey = 'id_entreprises_secteurs_activites';
    public $timestamps = false;

    protected $fillable = ['nom_secteur'];
}
