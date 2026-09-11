<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursAnnee extends Model
{
    protected $table = 'amos_cours_annees';
    public $timestamps = false;

    protected $fillable = ['id_unite_enseignement', 'id_cours', 'annee'];

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }
}
