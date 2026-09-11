<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    protected $table = 'amos_eleves';
    protected $primaryKey = 'id_eleve';
    public $timestamps = false;

    /**
     * Valeurs possibles de la colonne `profil` (portées depuis l'ENUM MySQL).
     */
    public const PROFIL_CANDIDAT = 'candidat';
    public const PROFIL_ELEVE = 'eleve';
    public const PROFIL_ALUMNI = 'alumni';
    public const PROFIL_REINSCRIT = 'reinscrit';
    public const PROFIL_ABANDON = 'abandon';

    protected $fillable = [
        'id_contact', 'id_eleve_parent', 'id_niveau', 'id_niveau_future', 'id_classe',
        'profil', 'valide', 'visible', 'date_inscription', 'date_depot',
        'numero_social', 'montant_formation', 'paiement_formation',
        'paiement_valide', 'commentaire', 'photo', 'visible_attente_epreuve',
        'lang_maternelle', 'situation_famille', 'avoir_enfants', 'nbr_enfants',
        'situation_actuelle', 'bac_obtenu_encours', 'situation_actuelle_autre',
        'formations_complementaires', 'duree_experience_pro', 'unite_experience_pro',
        'motivations', 'competences', 'autres_competences', 'qualites', 'defauts',
        'carte_identite', 'diplomes', 'releves_notes', 'cv', 'lettre_motivation',
        'rappel_paiement', 'dernier_epreuve', 'compteur_epreuve',
        'reaffectation_manuelle_epreuve', 'rappel_epreuve', 'echelonnement',
        'presence_eleve', 'attente_epreuve', 'importe', 'cacher_encaissement',
        'num_facture', 'groupes', 'specialisations',
    ];

    protected $casts = [
        'valide' => 'boolean',
        'visible' => 'boolean',
        'visible_attente_epreuve' => 'boolean',
        'paiement_valide' => 'boolean',
        'importe' => 'boolean',
        'presence_eleve' => 'boolean',
        'date_inscription' => 'datetime',
        'date_depot' => 'datetime',
        'date_debut_exclusion' => 'date',
        'date_fin_exclusion' => 'date',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id_contact', 'id_contact');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function niveauFuture()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau_future', 'id_niveau');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_classe', 'id_classe');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'id_eleve_parent', 'id_eleve');
    }

    public function enfants()
    {
        return $this->hasMany(self::class, 'id_eleve_parent', 'id_eleve');
    }

    public function groupes()
    {
        return $this->belongsToMany(
            GroupeEleve::class,
            'amos_eleves_groupes_refs',
            'id_eleve',
            'id_groupe'
        );
    }

    public function compteUtilisateur()
    {
        return $this->hasOne(UserEleve::class, 'id_eleve', 'id_eleve');
    }

    public function epreuvesInscriptions()
    {
        return $this->hasMany(EpreuveAdmissionEleve::class, 'id_eleve', 'id_eleve');
    }

    public function resultatsEpreuves()
    {
        return $this->hasMany(ResultatEpreuveEleve::class, 'id_eleve', 'id_eleve');
    }

    /** Équivalent de fetch_eleves($datas) côté CodeIgniter (profil = 'eleve', visible = 1). */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('profil', self::PROFIL_ELEVE)->where('visible', true);
    }

    public function scopeCandidats(Builder $query): Builder
    {
        return $query->where('profil', self::PROFIL_CANDIDAT);
    }

    public function scopeAbandons(Builder $query): Builder
    {
        return $query->where('profil', self::PROFIL_ABANDON);
    }
}
