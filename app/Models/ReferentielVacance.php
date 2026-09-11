<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Portage de `Parametrage_planning.php` (CodeIgniter) — une même table sert
 * aux 6 variantes legacy (`add_ferie`/`add_vacance`/`add_fermeture`/
 * `add_event`/`add_sejour`/`add_stage`) via la colonne `type`.
 */
class ReferentielVacance extends Model
{
    protected $table = 'amos_referentiel_vacance';
    protected $primaryKey = 'id_referentiel_vacance';
    public $timestamps = false;

    public const TYPES = ['ferie', 'vacance', 'stage', 'fermeture', 'event', 'sejour', 'partiel'];

    protected $fillable = [
        'titre', 'date_debut', 'date_fin', 'type', 'id_etablissement', 'id_classe',
        'date_creation', 'ip', 'id_cours', 'is_recurrence', 'all_day',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'date_creation' => 'datetime',
        'is_recurrence' => 'boolean',
        'all_day' => 'boolean',
    ];

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
