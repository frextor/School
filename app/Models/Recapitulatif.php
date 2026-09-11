<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `recapitulatif_model` (table `amos_recapitulatif`).
 *
 * NOTE fidèle au legacy : cette table ne contient aucune colonne
 * `id_intervenant` — les entrées ne sont pas rattachées à l'intervenant qui
 * les a saisies, seulement à un établissement/classe/cours. La liste est
 * donc globale (tous intervenants confondus), filtrable par établissement
 * uniquement, exactement comme `Recapitulatif_model::fetch_recapitulatif()`.
 */
class Recapitulatif extends Model
{
    protected $table = 'amos_recapitulatif';
    protected $primaryKey = 'id_recapitulatif';
    public $timestamps = false;

    protected $fillable = [
        'date_recapitulatif', 'id_classe', 'id_type_cours', 'hdebut', 'hfin',
        'id_cours', 'volume_horaire', 'id_etablissement',
    ];

    protected $casts = ['date_recapitulatif' => 'date'];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_classe', 'id_classe');
    }

    public function typeCours()
    {
        return $this->belongsTo(TypeCours::class, 'id_type_cours', 'id_type_cours');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }
}
