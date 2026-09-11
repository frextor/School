<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Portage d'une note d'élève (table `amos_sn_base_notes`). */
class Note extends Model
{
    protected $table = 'amos_sn_base_notes';
    protected $primaryKey = 'id_note';
    public $timestamps = false;

    protected $fillable = [
        'id_campus', 'annee', 'semestre', 'id_referentiel', 'id_ue', 'id_matiere',
        'id_type', 'id_evaluation', 'eval_session', 'id_eleve', 'validation_sans_note',
        'note', 'publier_admin', 'publier_eleve', 'date_saisie', 'session', 'referentiel',
    ];

    protected $casts = [
        'publier_admin' => 'boolean',
        'publier_eleve' => 'boolean',
        'validation_sans_note' => 'boolean',
        'date_saisie' => 'datetime',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'id_evaluation', 'id_evaluation');
    }

    public function historique()
    {
        return $this->hasMany(NoteHistorique::class, 'id_note', 'id_note');
    }
}
