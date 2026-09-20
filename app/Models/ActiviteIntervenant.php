<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage minimal de `Planning.php` (CodeIgniter) — un créneau de cours
 * planifié (table `amos_activite_intervenant`).
 *
 * NON couvert par ce modèle/module (voir PlanningController) : la détection
 * de conflits de disponibilité intervenant (`allow_insert_test`,
 * `adapte_disponibilite_intervenants`), les événements récurrents
 * (`add_recurrence`/`update_recurrence`, `recurrences_library`), et les
 * vues calendrier (jour/semaine/année par classe, intervenant ou
 * établissement). Ce sont des chantiers à part entière — voir la décision
 * prise avec l'utilisateur (CRUD simple d'abord).
 */
class ActiviteIntervenant extends Model
{
    protected $table = 'amos_activite_intervenant';
    protected $primaryKey = 'id_activite_intervenant';
    public $timestamps = false;

    protected $fillable = [
        'id_intervenant', 'id_etablissement', 'id_cours', 'id_classe', 'id_salle',
        'id_groupe', 'date_debut', 'date_fin', 'annotation', 'annotation_etudiant',
        'annotation_intervenant', 'groupe', 'semestre', 'annee', 'is_recurrence', 'all_day',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'is_recurrence' => 'boolean',
        'all_day' => 'boolean',
    ];

    public function intervenant()
    {
        return $this->belongsTo(Intervenant::class, 'id_intervenant', 'id_intervenant');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'id_etablissement', 'id_etablissement');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_classe', 'id_classe');
    }

    /**
     * `id_salle` est une colonne texte héritée, mais elle contient bien l'ID
     * d'une salle : sans cette relation, l'emploi du temps affichait
     * « Salle 43 » au lieu de « Salle A1 ».
     */
    public function salle()
    {
        return $this->belongsTo(Salle::class, 'id_salle', 'id_salle');
    }
}
