<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `Bulletins_model` (table `amos_eleve_bulletin`) — métadonnées
 * administratives d'un bulletin (commentaire, décision de jury, statut de
 * publication) pour un élève/classe et une période donnés.
 *
 * NE couvre PAS le calcul des moyennes ni le contenu détaillé du bulletin
 * (`bulletin_json`, snapshot généré par `bulletins_generer()` côté legacy à
 * partir des notes) : voir `EvaluationController`/`NoteController` pour les
 * notes brutes. La génération du contenu agrégé reste à faire séparément.
 */
class BulletinEleve extends Model
{
    protected $table = 'amos_eleve_bulletin';
    protected $primaryKey = 'id_bls';
    const CREATED_AT = 'date_create';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_eleve', 'id_etablissement', 'annee', 'id_referentiel', 'semestre',
        'session', 'commentaire', 'decision_jury', 'bulletin_json', 'est_publie',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_referentiel', 'id_classe');
    }
}
