<?php

namespace App\Support;

use App\Models\AbsenceEleve;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Enregistrement d'un appel, partagé par l'écran d'administration
 * (`AbsenceController`) et par l'espace enseignant, pour que les deux
 * produisent exactement les mêmes lignes d'assiduité.
 *
 * L'opération est idempotente : refaire l'appel d'une séance corrige la
 * saisie au lieu de la dupliquer, et un élève repassé « présent » voit sa
 * ligne retirée.
 */
class Appel
{
    public const PRESENT = 'present';

    /** Statuts acceptés dans un appel (règle de validation partagée). */
    public const STATUTS = 'in:present,absence,retard';

    /**
     * @param  array<int|string, string>  $statuts  id_eleve => present|absence|retard
     * @return array{absence: int, retard: int, efface: int}
     */
    public static function enregistrer(array $statuts, CarbonInterface $date, string $heure, ?int $idCours = null): array
    {
        $jour = $date->format('Y-m-d');
        $heurePleine = $heure.':00';
        $semestre = AbsenceEleve::semestrePour($date);
        $compte = ['absence' => 0, 'retard' => 0, 'efface' => 0];

        DB::transaction(function () use ($statuts, $jour, $heurePleine, $semestre, $idCours, &$compte) {
            foreach ($statuts as $idEleve => $statut) {
                $existante = AbsenceEleve::where('id_eleve', (int) $idEleve)
                    ->where('date_absence', $jour)
                    ->where('heure_absence', $heurePleine)
                    ->first();

                if ($statut === self::PRESENT) {
                    if ($existante) {
                        $existante->delete();
                        $compte['efface']++;
                    }

                    continue;
                }

                // Une saisie existante conserve sa justification : l'appel ne
                // doit pas effacer un justificatif déjà fourni par la famille.
                $justifie = $existante?->justifie ?? false;

                AbsenceEleve::updateOrCreate(
                    [
                        'id_eleve' => (int) $idEleve,
                        'date_absence' => $jour,
                        'heure_absence' => $heurePleine,
                    ],
                    AbsenceEleve::drapeaux($statut, $justifie) + [
                        'id_cours' => $idCours ?: 0,
                        'id_unite_enseignement' => 0,
                        'semestre' => $semestre,
                        'valide' => 1,
                        'annotation' => $existante->annotation ?? '',
                        'justificatif' => $justifie,
                        'modification_justificatif' => 0,
                    ]
                );

                $compte[$statut]++;
            }
        });

        return $compte;
    }

    /** Phrase de retour affichée après un appel. */
    public static function message(array $compte): string
    {
        return "Appel enregistré : {$compte['absence']} absence(s), {$compte['retard']} retard(s)"
            .($compte['efface'] ? ", {$compte['efface']} ligne(s) retirée(s)" : '').'.';
    }
}
