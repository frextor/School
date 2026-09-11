<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_volumes_formations`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class VolumesFormations extends Model
{
    protected $table = 'amos_volumes_formations';
    protected $primaryKey = 'id_volume_formation';
    public $timestamps = false;

    protected $fillable = [
        'effectif',
        'nb_classe',
        'volume_cours',
        'id_niveau',
        'id_etablissement',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }
}
