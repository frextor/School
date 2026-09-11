<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $table = 'amos_salles';
    protected $primaryKey = 'id_salle';
    public $timestamps = false;

    protected $fillable = ['code_salle', 'nom_salle', 'nombre_place', 'id_etablissement', 'date_creation', 'ip'];

    protected $casts = ['date_creation' => 'datetime'];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }
}
