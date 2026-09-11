<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de "Types de documents" dans `Referentiel.php` (get_types_document
 * etc, appuyé sur `entreprises_model->fetch_type_piece`).
 * Table `amos_entreprises_eleves_types_piece` : types de pièces justificatives
 * demandées aux entreprises d'accueil des élèves en alternance/stage.
 */
class TypePieceEntreprise extends Model
{
    protected $table = 'amos_entreprises_eleves_types_piece';
    protected $primaryKey = 'id_type_piece';
    public $timestamps = false;

    protected $fillable = ['type', 'periode_associee'];

    protected $casts = ['periode_associee' => 'boolean'];
}
