<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeTache extends Model
{
    protected $table = 'amos_types_taches';
    protected $primaryKey = 'id_type_tache';

    protected $fillable = ['libelle'];
}
