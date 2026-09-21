<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCours extends Model
{
    protected $table = 'amos_type_cours';
    protected $primaryKey = 'id_type_cours';
    public $timestamps = false;

    protected $fillable = ['type_cours'];

    /**
     * Natures d'heures déclarables par un enseignant dans une école K-12.
     * La table était vide : le récapitulatif d'heures exigeait un type, donc
     * aucune saisie n'était possible.
     */
    public const TYPES_MAROC = [
        'Cours',
        'Soutien scolaire',
        'Activité parascolaire',
        'Surveillance',
        'Réunion pédagogique',
    ];
}
