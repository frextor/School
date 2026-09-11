<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursCompetence extends Model
{
    protected $table = 'amos_cours_competences';
    protected $primaryKey = 'id_competence';
    public $timestamps = false;

    protected $fillable = ['id_cours', 'annee', 'id_annee_formation', 'competences'];

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }
}
