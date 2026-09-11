<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `intervenant_model` (table `amos_intervenant`).
 *
 * NON couvert par ce modèle : la création d'un compte de connexion
 * (`amos_users_intervenant`) et l'envoi d'email de bienvenue — ce sont deux
 * sous-systèmes distincts (guard d'authentification intervenant + moteur de
 * templates email) à traiter séparément.
 */
class Intervenant extends Model
{
    protected $table = 'amos_intervenant';
    protected $primaryKey = 'id_intervenant';
    public $timestamps = false;

    protected $fillable = [
        'civilite', 'nom', 'prenom', 'email', 'email_office', 'date_naissance',
        'id_nationalite', 'id_langue', 'telephone', 'mobile', 'adresse',
        'code_postal', 'ville', 'id_pays', 'formation_suivie_intitule',
        'niveau_formation_suivie', 'lieu_formation_suivie', 'profession',
        'cv', 'photo', 'id_societe', 'poste_actuel', 'signature',
    ];

    protected $casts = ['date_naissance' => 'date'];

    public function societe()
    {
        return $this->belongsTo(IntervenantSociete::class, 'id_societe', 'id_societe');
    }

    public function competences()
    {
        return $this->hasMany(IntervenantCompetence::class, 'id_intervenant', 'id_intervenant');
    }

    public function diplomes()
    {
        return $this->hasMany(IntervenantDiplome::class, 'id_intervenant', 'id_intervenant');
    }

    public function secteursActivite()
    {
        return $this->hasMany(IntervenantSecteurActivite::class, 'id_intervenant', 'id_intervenant');
    }

    public function cours()
    {
        return $this->belongsToMany(
            Cours::class,
            'amos_intervenant_cours',
            'id_intervenant',
            'id_cours'
        );
    }

    public function etablissements()
    {
        return $this->belongsToMany(
            Etablissement::class,
            'amos_intervenant_etablissement',
            'id_intervenant',
            'id_etablissement'
        );
    }

    public function cheminCv(): ?string
    {
        return $this->cv ? "intervenants/cv/{$this->cv}" : null;
    }

    public function cheminPhoto(): ?string
    {
        return $this->photo ? "intervenants/photos/{$this->photo}" : null;
    }
}
