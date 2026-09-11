<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `amos_eleve_reglements`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class EleveReglements extends Model
{
    protected $table = 'amos_eleve_reglements';
    protected $primaryKey = 'id_eleve_reglement';
    public $timestamps = false;

    protected $fillable = [
        'id_saisie',
        'id_eleve',
        'transaction_id',
        'titre',
        'mode',
        'date',
        'montant',
        'nom_banque',
        'numero_cheque',
        'commentaires',
        'photo_cheque',
        'id_etablissement',
        'archive',
    ];
}
