<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntervenantSociete extends Model
{
    protected $table = 'amos_intervenant_societe';
    protected $primaryKey = 'id_societe';
    public $timestamps = false;

    protected $fillable = [
        'raison_sociale', 'adresse_societe', 'tel_societe', 'fax_societe', 'email_societe', 'id_secteur_activite',
    ];
}
