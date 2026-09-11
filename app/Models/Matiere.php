<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'amos_matiere';
    protected $primaryKey = 'id_matiere';
    public $timestamps = false;

    protected $fillable = ['code_matiere', 'nom_matiere', 'id_unite_enseignement'];

    public function unite()
    {
        return $this->belongsTo(UniteEnseignement::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }
}
