<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage minimal de `entreprises_model` (table `amos_entreprises`).
 *
 * Ce modèle ne couvre que l'identité de base de l'entreprise. La gestion
 * complète (contacts, missions, relances, sources, secteurs...) appartient
 * au module Entreprises.php (2267 lignes, non encore migré) et sera ajoutée
 * à ce modèle au fur et à mesure de ce chantier plutôt que dupliquée ici.
 */
class Entreprise extends Model
{
    protected $table = 'amos_entreprises';
    protected $primaryKey = 'id_entreprise';

    protected $fillable = [
        'id_parent', 'type_entreprise', 'nom_entreprise', 'adresse', 'code_postal',
        'ville', 'pays', 'telephone', 'email', 'id_etablissement', 'site_web',
        'contact', 'information_complementaire', 'siret', 'numero_tva', 'id_secteur',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'id_parent', 'id_entreprise');
    }

    public function filiales()
    {
        return $this->hasMany(self::class, 'id_parent', 'id_entreprise');
    }

    public function compteLoginPortail()
    {
        return $this->hasOne(EntreprisePortail::class, 'id_entreprise', 'id_entreprise');
    }

    public function contacts()
    {
        return $this->hasMany(EntrepriseContact::class, 'id_entreprise', 'id_entreprise');
    }

    public function secteur()
    {
        return $this->belongsTo(EntrepriseSecteurActivite::class, 'id_secteur', 'id_entreprises_secteurs_activites');
    }
}
