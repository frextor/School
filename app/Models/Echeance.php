<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Une échéance de scolarité : frais d'inscription ou mensualité.
 *
 * Le statut n'est pas stocké mais déduit du montant réglé et de la date :
 * une colonne `statut` se désynchroniserait dès qu'un règlement est corrigé.
 */
class Echeance extends Model
{
    protected $table = 'echeances';
    protected $primaryKey = 'id_echeance';

    public const TYPE_INSCRIPTION = 'inscription';
    public const TYPE_MENSUALITE = 'mensualite';
    public const TYPE_OPTION = 'option';
    public const TYPE_AUTRE = 'autre';

    public const STATUT_PAYEE = 'payee';
    public const STATUT_PARTIELLE = 'partielle';
    public const STATUT_RETARD = 'retard';
    public const STATUT_A_VENIR = 'a_venir';

    public const STATUTS = [
        self::STATUT_PAYEE => 'Payée',
        self::STATUT_PARTIELLE => 'Partielle',
        self::STATUT_RETARD => 'En retard',
        self::STATUT_A_VENIR => 'À venir',
    ];

    protected $fillable = [
        'id_eleve', 'annee_scolaire', 'type', 'libelle', 'montant',
        'date_echeance', 'montant_regle', 'date_reglement', 'mode_reglement',
        'commentaire',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_reglement' => 'date',
        'montant' => 'decimal:2',
        'montant_regle' => 'decimal:2',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'id_eleve', 'id_eleve');
    }

    public function getResteAttribute(): float
    {
        return max(0, (float) $this->montant - (float) $this->montant_regle);
    }

    public function getStatutAttribute(): string
    {
        if ($this->reste <= 0) {
            return self::STATUT_PAYEE;
        }

        if ((float) $this->montant_regle > 0) {
            return self::STATUT_PARTIELLE;
        }

        return $this->date_echeance?->isPast() ? self::STATUT_RETARD : self::STATUT_A_VENIR;
    }

    public function getStatutLibelleAttribute(): string
    {
        return self::STATUTS[$this->statut];
    }

    /** Attendu par l'onglet « Règlements » de la fiche élève. */
    public function getEncaisseAttribute(): bool
    {
        return $this->statut === self::STATUT_PAYEE;
    }

    /** Idem : la vue lit `moyen` là où la colonne s'appelle `mode_reglement`. */
    public function getMoyenAttribute(): ?string
    {
        return $this->mode_reglement;
    }

    /** Échéances dues et non soldées (le cœur du suivi des impayés). */
    public function scopeImpayees(Builder $query): Builder
    {
        return $query->whereColumn('montant_regle', '<', 'montant')
            ->whereDate('date_echeance', '<=', Carbon::today());
    }

    public function scopeAnnee(Builder $query, ?string $annee): Builder
    {
        return $annee ? $query->where('annee_scolaire', $annee) : $query;
    }

    /** Année scolaire courante au format « 2026-2027 » (bascule en août). */
    public static function anneeScolaireCourante(): string
    {
        $debut = (int) date('n') >= 8 ? (int) date('Y') : (int) date('Y') - 1;

        return $debut.'-'.($debut + 1);
    }
}
