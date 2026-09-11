<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniteEnseignementAnnee extends Model
{
    protected $table = 'amos_unite_enseignement_annees';
    public $timestamps = false;

    protected $fillable = ['id_unite_enseignement', 'annee'];

    public function unite()
    {
        return $this->belongsTo(UniteEnseignement::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }
}
