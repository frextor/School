<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_etablissement_regle_session`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EtablissementRegleSession extends Model
{
    protected $table = 'amos_etablissement_regle_session';
    protected $primaryKey = 'id_rules';
    public $timestamps = false;

    protected $fillable = [
        'id_etablissement',
        'moyenne_UE_inf_declenchement_annee_1',
        'moyenne_UE_inf_declenchement_annee_2',
        'moyenne_UE_inf_declenchement_annee_3',
        'moyenne_UE_inf_declenchement_annee_4',
        'moyenne_UE_inf_declenchement_annee_5',
    ];
}
