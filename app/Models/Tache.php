<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage du sous-système "tâches CRM" (relances) de `Crm.php`
 * (nouvelle_tache / rappel_immediat / cloture_tache), table `amos_taches`.
 */
class Tache extends Model
{
    protected $table = 'amos_taches';
    protected $primaryKey = 'id_tache';

    /** Statut "1" = ouverte par défaut côté legacy (colonne DEFAULT '1'). */
    public const STATUT_OUVERTE = 1;

    protected $fillable = [
        'id_contact', 'id_admin_assigne', 'id_service_assigne', 'id_ecole_assigne',
        'type_tache', 'objet', 'commentaire', 'date_debut', 'deadline',
        'date_realisation', 'score', 'id_type_tache', 'id_statut_contact',
        'id_statut_tache', 'archive',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'deadline' => 'datetime',
        'date_realisation' => 'date',
        'archive' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id_contact', 'id_contact');
    }

    public function adminAssigne()
    {
        return $this->belongsTo(Admin::class, 'id_admin_assigne', 'id_admin');
    }

    public function statut()
    {
        return $this->belongsTo(StatutTache::class, 'id_statut_tache', 'id_statut_tache');
    }

    public function type()
    {
        return $this->belongsTo(TypeTache::class, 'id_type_tache', 'id_type_tache');
    }

    public function scopeOuvertes($query)
    {
        return $query->where('archive', false);
    }
}
