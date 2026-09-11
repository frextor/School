<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configuration d'un type d'évaluation pour un contexte donné (établissement,
 * UE, cours, niveau/classe-ou-groupe, année, semestre) avec son coefficient.
 * Table `amos_sn_type_evaluation`.
 */
class SnTypeEvaluation extends Model
{
    protected $table = 'amos_sn_type_evaluation';
    protected $primaryKey = 'id_type_evaluation';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement', 'coef', 'id_unite_enseignement', 'id_cours', 'id_niveau',
        'id_referentiel', 'id_type', 'annee', 'semestre', 'referentiel',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    public function unite()
    {
        return $this->belongsTo(UniteEnseignement::class, 'id_unite_enseignement', 'id_unite_enseignement');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function type()
    {
        return $this->belongsTo(TypeEvaluation::class, 'id_type', 'id_type');
    }
}
