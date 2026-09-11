<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaiementEleveOption extends Model
{
    protected $table = 'amos_eleve_paiements_options';
    protected $primaryKey = 'id_eleve_paiements_options';
    public $timestamps = false;

    protected $fillable = ['id_paiement_eleve', 'id_eleve', 'id_niveau_option', 'montant'];

    public function paiement()
    {
        return $this->belongsTo(PaiementEleve::class, 'id_paiement_eleve', 'id_paiement_eleve');
    }
}
