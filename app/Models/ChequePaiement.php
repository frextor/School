<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequePaiement extends Model
{
    protected $table = 'amos_cheques_paiement';
    protected $primaryKey = 'id_cheque_paiement';
    public $timestamps = false;

    protected $fillable = [
        'photo_cheque', 'numero_cheque', 'montant_paiement', 'date_encaissement',
        'nom_banque', 'id_paiement_eleve', 'id_objet_paiement', 'date_echeance',
        'id_etablissement', 'mode_paiement', 'paiement_recu', 'cheque_caution',
        'numero_echeance', 'echeance',
    ];

    protected $casts = [
        'date_encaissement' => 'date',
        'date_echeance' => 'date',
        'paiement_recu' => 'boolean',
        'cheque_caution' => 'boolean',
    ];

    public function paiement()
    {
        return $this->belongsTo(PaiementEleve::class, 'id_paiement_eleve', 'id_paiement_eleve');
    }
}
