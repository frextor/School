<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $table = 'amos_classe';
    protected $primaryKey = 'id_classe';
    public $timestamps = false;

    protected $fillable = [
        'code_classe', 'classe', 'id_niveau', 'id_etablissement', 'couleur',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'id_classe', 'id_classe');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }
}
