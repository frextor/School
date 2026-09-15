<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Absence ou retard d'un élève sur une séance (table legacy `amos_absence_eleve`).
 *
 * Le schéma legacy n'a pas de colonne « type » : il encode la nature et la
 * justification sur quatre booléens exclusifs
 * (`absences_non_justifies`, `absences_excuses`, `retards_non_justifies`,
 * `retards_excuses`). On garde la table telle quelle — elle est correctement
 * indexée et déjà reliée aux bulletins — mais on expose ici une API lisible
 * (`nature`, `justifie`, scopes) pour ne pas manipuler ces flags dans les
 * contrôleurs et les vues.
 */
class AbsenceEleve extends Model
{
    protected $table = 'amos_absence_eleve';
    protected $primaryKey = 'id_absence';
    public $timestamps = false;

    public const NATURE_ABSENCE = 'absence';
    public const NATURE_RETARD = 'retard';

    public const NATURES = [
        self::NATURE_ABSENCE => 'Absence',
        self::NATURE_RETARD => 'Retard',
    ];

    protected $fillable = [
        'date_absence', 'heure_absence', 'id_cours', 'retards_non_justifies',
        'retards_excuses', 'absences_non_justifies', 'absences_excuses',
        'id_eleve', 'id_unite_enseignement', 'annotation', 'semestre', 'valide',
        'justificatif', 'justificatif_fichers', 'date_justificatif',
        'modification_justificatif',
    ];

    protected $casts = [
        'date_absence' => 'date',
        'date_justificatif' => 'datetime',
        'retards_non_justifies' => 'boolean',
        'retards_excuses' => 'boolean',
        'absences_non_justifies' => 'boolean',
        'absences_excuses' => 'boolean',
        'justificatif' => 'boolean',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }

    /**
     * Traduit (nature, justifiée) vers les quatre booléens du schéma legacy.
     * Point de passage unique : toute écriture doit passer par ici.
     */
    public static function drapeaux(string $nature, bool $justifie): array
    {
        $estRetard = $nature === self::NATURE_RETARD;

        return [
            'absences_non_justifies' => ! $estRetard && ! $justifie,
            'absences_excuses' => ! $estRetard && $justifie,
            'retards_non_justifies' => $estRetard && ! $justifie,
            'retards_excuses' => $estRetard && $justifie,
        ];
    }

    public function getNatureAttribute(): string
    {
        return ($this->retards_non_justifies || $this->retards_excuses)
            ? self::NATURE_RETARD
            : self::NATURE_ABSENCE;
    }

    public function getJustifieAttribute(): bool
    {
        return (bool) ($this->absences_excuses || $this->retards_excuses);
    }

    public function getNatureLibelleAttribute(): string
    {
        return self::NATURES[$this->nature];
    }

    /** Semestre scolaire marocain : S1 de septembre à janvier, S2 de février à août. */
    public static function semestrePour(\DateTimeInterface $date): int
    {
        $mois = (int) $date->format('n');

        return ($mois >= 9 || $mois === 1) ? 1 : 2;
    }

    public function scopeNonJustifiees(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->where('absences_non_justifies', true)
            ->orWhere('retards_non_justifies', true));
    }

    public function scopeAbsences(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->where('absences_non_justifies', true)
            ->orWhere('absences_excuses', true));
    }

    public function scopeRetards(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->where('retards_non_justifies', true)
            ->orWhere('retards_excuses', true));
    }
}
