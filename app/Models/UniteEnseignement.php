<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage de `unite_model` (table `amos_unite_enseignement`). */
class UniteEnseignement extends Model
{
    protected $table = 'amos_unite_enseignement';
    protected $primaryKey = 'id_unite_enseignement';
    public $timestamps = false;

    protected $fillable = ['code_unite', 'nom_unite_enseignement', 'id_niveau', 'couleur'];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function cours()
    {
        return $this->hasMany(Cours::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }

    public function matieres()
    {
        return $this->hasMany(Matiere::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }

    /** Années de formation concernées par cette UE (table pivot amos_unite_enseignement_annees). */
    public function annees()
    {
        return $this->hasMany(UniteEnseignementAnnee::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }
}
