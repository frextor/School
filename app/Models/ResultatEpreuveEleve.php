<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatEpreuveEleve extends Model
{
    protected $table = 'amos_resultats_epreuve_eleve';
    protected $primaryKey = 'id_resultat_epreuve_eleve';
    public $timestamps = false;

    public const DECISION_ACCEPTE = 'accepte';
    public const DECISION_ACCEPTE_NIVEAU_INFERIEUR = 'accepter_niveau_inferieur';
    public const DECISION_REFUSE = 'refuse';
    public const DECISION_EN_ATTENTE = 'en_attente';
    public const DECISION_ACCEPTE_AVEC_ENTREPRISE = 'accepter_avec_entreprise';

    protected $fillable = [
        'id_eleve', 'id_epreuve_admission', 'anglais', 'culture_generale',
        'epreuve_redaction', 'entretien', 'decision', 'id_motif_refus',
        'archive', 'date_operation',
    ];

    protected $casts = [
        'archive' => 'boolean',
        'date_operation' => 'datetime',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function epreuve()
    {
        return $this->belongsTo(EpreuveAdmission::class, 'id_epreuve_admission', 'id_epreuve_admission');
    }
}
