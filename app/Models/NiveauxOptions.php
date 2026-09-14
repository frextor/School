<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catalogue des options facturables par niveau/année (table `amos_niveaux_options`).
 * Portage de `niveau_option_model` (legacy) — chaque ligne est une option
 * proposable sur un règlement (`Eleves.php::add_reglements()`), avec son
 * montant par défaut. Géré depuis l'écran d'édition d'un niveau
 * (`referentiel.niveaux.edit`) via `NiveauOptionController`.
 */
class NiveauxOptions extends Model
{
    protected $table = 'amos_niveaux_options';
    protected $primaryKey = 'id_niveau_option';
    public $timestamps = false;

    protected $fillable = [
        'id_niveau',
        'id_objet_paiement',
        'titre',
        'montant',
        'ordre',
        'annee',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function objetPaiement()
    {
        return $this->belongsTo(ObjetPaiement::class, 'id_objet_paiement', 'id_objet_paiement');
    }
}
