<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Parent / tuteur légal d'un élève (orientation K-12).
 *
 * Une fiche par personne, reliée à N élèves via `eleve_tuteur` : une fratrie
 * partage donc la même fiche tuteur (voir la migration pour le raisonnement).
 *
 * Nommé `Tuteur` et non `Parent` : `Parent` est un mot réservé de PHP.
 */
class Tuteur extends Model
{
    protected $table = 'tuteurs';
    protected $primaryKey = 'id_tuteur';

    /** Liens de parenté proposés dans les formulaires. */
    public const LIENS = [
        'pere' => 'Père',
        'mere' => 'Mère',
        'tuteur' => 'Tuteur légal',
        'autre' => 'Autre',
    ];

    protected $fillable = [
        'civilite', 'nom', 'prenom', 'email', 'telephone', 'telephone_pro',
        'profession', 'cin', 'adresse', 'code_postal', 'ville', 'commentaire',
    ];

    public function eleves()
    {
        return $this->belongsToMany(Eleve::class, 'eleve_tuteur', 'id_tuteur', 'id_eleve')
            ->withPivot(['lien_parente', 'responsable_legal', 'contact_urgence', 'ordre'])
            ->withTimestamps();
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    /** Libellé lisible du lien de parenté porté par la liaison courante. */
    public function getLienLibelleAttribute(): string
    {
        $lien = $this->pivot?->lien_parente;

        return self::LIENS[$lien] ?? self::LIENS['autre'];
    }
}
