<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_referentiel_niveau`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class ReferentielNiveau extends Model
{
    protected $table = 'amos_referentiel_niveau';
    protected $primaryKey = 'id_referentiel_niveau';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'id_unite_enseignement',
        'anne',
        'id_niveau',
        'semestre',
        'id_cours',
        'cc',
        'cr',
        'td',
        'ei',
        'cc_val',
        'cr_val',
        'td_val',
        'ei_val',
        'id_intervenant',
        'volume',
        'ects',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    public function intervenant()
    {
        return $this->belongsTo(Intervenant::class, 'id_intervenant', 'id_intervenant');
    }
}
