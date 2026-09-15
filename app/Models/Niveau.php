<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $table = 'amos_niveaux';
    protected $primaryKey = 'id_niveau';
    public $timestamps = false;

    protected $fillable = [
        'code_niveau', 'id_formation', 'id_diplome', 'nom_niveau',
        'deuxieme_langue', 'id_niveau_future',
    ];

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'id_niveau', 'id_niveau');
    }

    public function classes()
    {
        return $this->hasMany(Classe::class, 'id_niveau', 'id_niveau');
    }

    public function niveauFuture()
    {
        return $this->belongsTo(self::class, 'id_niveau_future', 'id_niveau');
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class, 'id_formation', 'id_formation');
    }

    public function unitesEnseignement()
    {
        return $this->hasMany(UniteEnseignement::class, 'id_niveau', 'id_niveau');
    }

    /**
     * Matières enseignées à ce niveau, avec leur coefficient (K-12).
     * La matière est un `amos_cours` : c'est la table que `id_matiere` référence
     * dans toute la notation.
     */
    public function matieres()
    {
        return $this->belongsToMany(Cours::class, 'matiere_niveau', 'id_niveau', 'id_cours')
            ->withPivot(['coefficient', 'ordre'])
            ->withTimestamps()
            ->orderBy('matiere_niveau.ordre');
    }

    /** Catalogue des options facturables (frais de dossier, assurance, etc.) pour ce niveau. */
    public function options()
    {
        return $this->hasMany(NiveauxOptions::class, 'id_niveau', 'id_niveau');
    }

    /** Campus rattachés à ce niveau (table pivot amos_etablissements_niveaux). */
    public function etablissements()
    {
        return $this->belongsToMany(
            Etablissement::class,
            'amos_etablissements_niveaux',
            'id_niveau',
            'id_etablissement'
        );
    }
}
