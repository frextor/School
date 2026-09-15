<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `cours_model` (table `amos_cours`). */
class Cours extends Model
{
    protected $table = 'amos_cours';
    protected $primaryKey = 'id_cours';
    public $timestamps = false;

    protected $fillable = ['code_cours', 'nom_cours', 'id_unite_enseignement'];

    public function unite()
    {
        return $this->belongsTo(UniteEnseignement::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }

    public function annees()
    {
        return $this->hasMany(CoursAnnee::class, 'id_cours', 'id_cours');
    }

    /** Niveaux où cette matière est enseignée, avec son coefficient (K-12). */
    public function niveaux()
    {
        return $this->belongsToMany(Niveau::class, 'matiere_niveau', 'id_cours', 'id_niveau')
            ->withPivot(['coefficient', 'ordre'])
            ->withTimestamps();
    }

    public function competences()
    {
        return $this->hasMany(CoursCompetence::class, 'id_cours', 'id_cours');
    }
}
