<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage d'une évaluation programmée (table `amos_sn_evaluations_existantes`). */
class Evaluation extends Model
{
    protected $table = 'amos_sn_evaluations_existantes';
    protected $primaryKey = 'id_evaluation';
    public $timestamps = false;

    protected $fillable = [
        'id_campus', 'annee', 'semestre', 'id_referentiel', 'id_ue', 'id_matiere',
        'id_type_evaluation', 'nom_evaluation', 'date_evaluation', 'heure_debut',
        'heure_fin', 'type_notation', 'boolean_facultatif', 'referentiel',
        'id_evaluation_parent',
    ];

    protected $casts = [
        'date_evaluation' => 'date',
        'boolean_facultatif' => 'boolean',
    ];

    public function campus()
    {
        return $this->belongsTo(Etablissement::class, 'id_campus', 'id_etablissement');
    }

    public function unite()
    {
        return $this->belongsTo(UniteEnseignement::class, 'id_ue', 'id_unite_enseignement');
    }

    public function matiere()
    {
        return $this->belongsTo(Cours::class, 'id_matiere', 'id_cours');
    }

    public function typeEvaluation()
    {
        return $this->belongsTo(SnTypeEvaluation::class, 'id_type_evaluation', 'id_type_evaluation');
    }

    /** Affiché sur le bulletin PDF (colonne "Coef.") : le coefficient réel du type d'évaluation, pas une valeur par défaut arbitraire. */
    public function getCoefficientAttribute()
    {
        return $this->typeEvaluation?->coef ?? 1;
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'id_evaluation', 'id_evaluation');
    }

    /** L'évaluation référence soit une classe, soit un groupe, selon `referentiel`. */
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_referentiel', 'id_classe');
    }

    public function groupe()
    {
        return $this->belongsTo(GroupeEleve::class, 'id_referentiel', 'id_groupe');
    }
}
