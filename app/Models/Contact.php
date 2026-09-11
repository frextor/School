<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'amos_contacts';
    protected $primaryKey = 'id_contact';
    public $timestamps = false;

    protected $fillable = [
        'id_contact_parent', 'civilite', 'nom', 'prenom', 'sexe', 'date_naissance', 'lieu_naissance',
        'pays_naissance', 'nationalite', 'adresse', 'code_postal', 'ville', 'pays',
        'telephone', 'email', 'id_formation', 'newsletter', 'offres_partenaires',
        'intitule_derniere_formation', 'lieu_derniere_formation', 'date_derniere_formation',
        'niveau_derniere_formation', 'diplome_derniere_formation', 'annotation',
        'annee_rentree', 'visible', 'stop_relances', 'last_update', 'source',
        'date_inscription', 'reunion_info', 'step', 'sexe', 'id_contact_parent',
        'email_office', 'comment_connaitre_amos', 'candidat', 'derniere_reunion',
        'compteur_reunion', 'reaffectation_manuelle', 'demande_brochure',
        'rappel_reunion', 'participe_reunion', 'salon', 'agent_de_joueur',
        'salon_nom', 'salon_ville', 'salon_date', 'suivre_actu', 'id_motif_abandon',
    ];

    protected $casts = [
        'newsletter' => 'boolean',
        'offres_partenaires' => 'boolean',
        'visible' => 'boolean',
        'stop_relances' => 'boolean',
        'date_inscription' => 'datetime',
        'last_update' => 'datetime',
    ];

    public function eleve()
    {
        return $this->hasOne(Eleve::class, 'id_contact', 'id_contact');
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class, 'id_formation', 'id_formation');
    }

    public function ecoles()
    {
        return $this->hasMany(ContactEcole::class, 'id_contact', 'id_contact');
    }

    public function inscriptionsReunion()
    {
        return $this->hasMany(ReunionInformationContact::class, 'id_contact', 'id_contact');
    }

    public function annotations()
    {
        return $this->hasMany(Annotation::class, 'contact_id', 'id_contact');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'id_contact', 'id_contact');
    }

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }
}
