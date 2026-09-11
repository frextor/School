<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutTache extends Model
{
    protected $table = 'amos_statuts_taches';
    protected $primaryKey = 'id_statut_tache';

    protected $fillable = ['libelle'];
}
