<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `panneaux_model` (table `amos_panneaux_lumineux`) — panneaux
 * d'affichage dynamique du planning des cours dans les établissements.
 */
class PanneauLumineux extends Model
{
    protected $table = 'amos_panneaux_lumineux';
    protected $primaryKey = 'id_panneau';
    public $timestamps = false;

    protected $fillable = ['id_etablissement', 'annee', 'identifiant_panneaux', 'titre', 'plage_horaire', 'delai_horaire'];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'amos_panneaux_formations', 'id_panneau', 'id_formation');
    }

    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'amos_panneaux_classes', 'id_panneau', 'id_classe');
    }

    public function groupes()
    {
        return $this->belongsToMany(GroupeEleve::class, 'amos_panneaux_groupes', 'id_panneau', 'id_groupe');
    }
}
