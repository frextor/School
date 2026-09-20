<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Mise en page d'une semaine de calendrier : range les créneaux par jour,
 * calcule la plage horaire à afficher et positionne chaque créneau (hauteur,
 * décalage, colonne quand deux cours se chevauchent).
 *
 * Volontairement sans dépendance : la vue n'a plus qu'à traduire des
 * pourcentages en CSS, et la logique reste la même pour l'écran
 * d'administration, l'espace élève et l'espace enseignant.
 */
class Calendrier
{
    /** Plage affichée au minimum, même une semaine vide ressemble à un emploi du temps. */
    public const HEURE_MIN = 8;
    public const HEURE_MAX = 18;

    /**
     * @param  iterable<array{debut: CarbonInterface, fin: CarbonInterface}>  $evenements
     *         Chaque élément est libre d'embarquer ses propres clés (titre,
     *         lieu, url…) : elles sont recopiées telles quelles.
     * @return array{jours: array<int, array{date: CarbonInterface, evenements: array}>, heure_min: int, heure_max: int, heures: array<int, int>, total: int}
     */
    public static function semaine(iterable $evenements, CarbonInterface $debutSemaine, int $nbJours = 6): array
    {
        $evenements = collect($evenements)->filter(fn ($e) => isset($e['debut'], $e['fin']))->values();

        [$heureMin, $heureMax] = self::plageHoraire($evenements);
        $amplitude = max(1, $heureMax - $heureMin);

        $jours = [];

        for ($i = 0; $i < $nbJours; $i++) {
            $date = $debutSemaine->copy()->addDays($i);

            $duJour = $evenements
                ->filter(fn ($e) => $e['debut']->isSameDay($date))
                ->sortBy(fn ($e) => $e['debut']->getTimestamp())
                ->values();

            $jours[] = [
                'date' => $date,
                'evenements' => self::positionner($duJour, $heureMin, $amplitude),
            ];
        }

        return [
            'jours' => $jours,
            'heure_min' => $heureMin,
            'heure_max' => $heureMax,
            'heures' => range($heureMin, $heureMax),
            'total' => $evenements->count(),
        ];
    }

    /**
     * Lundi de la semaine demandée. Une date illisible dans l'URL retombe sur
     * la semaine courante plutôt que de renvoyer une erreur à l'élève.
     */
    public static function debutSemaine(?string $date): Carbon
    {
        if (filled($date)) {
            try {
                return Carbon::parse($date)->startOfWeek();
            } catch (\Exception) {
                // Paramètre bricolé à la main : on l'ignore.
            }
        }

        return Carbon::now()->startOfWeek();
    }

    /**
     * Couleur stable d'une matière, là où il n'y a pas de couleur de classe à
     * reprendre (espace élève : toutes les lignes seraient du même indigo).
     * Le même nom donne toujours la même teinte d'une semaine à l'autre.
     */
    public static function couleurMatiere(?string $matiere): string
    {
        $palette = ['#4f46e5', '#0f766e', '#b45309', '#be185d', '#1d4ed8', '#15803d', '#7c3aed', '#c2410c'];

        if (! filled($matiere)) {
            return $palette[0];
        }

        return $palette[crc32($matiere) % count($palette)];
    }

    /**
     * Plage horaire réellement utile : 8 h – 18 h par défaut, élargie si des
     * cours débordent (un cours de 7 h 30 ne doit pas sortir de la grille).
     *
     * @return array{0: int, 1: int}
     */
    private static function plageHoraire(Collection $evenements): array
    {
        if ($evenements->isEmpty()) {
            return [self::HEURE_MIN, self::HEURE_MAX];
        }

        $premier = (int) $evenements->min(fn ($e) => (int) $e['debut']->format('H'));
        $dernier = (int) $evenements->max(fn ($e) => (int) ceil(
            (int) $e['fin']->format('H') + ((int) $e['fin']->format('i') > 0 ? 1 : 0)
        ));

        return [min(self::HEURE_MIN, $premier), max(self::HEURE_MAX, min(24, $dernier))];
    }

    /**
     * Positionne les créneaux d'une journée. Deux cours qui se chevauchent
     * partagent la largeur de la colonne : ils sont regroupés en « paquets »
     * de créneaux qui se recouvrent de proche en proche, et chacun prend la
     * première sous-colonne libre du paquet.
     */
    private static function positionner(Collection $duJour, int $heureMin, int $amplitude): array
    {
        $places = [];
        $paquet = [];
        $finPaquet = null;

        $vider = function () use (&$paquet, &$places, $heureMin, $amplitude) {
            $colonnes = [];
            $paquetPlaces = [];

            foreach ($paquet as $evenement) {
                $index = 0;

                // Première sous-colonne dont le dernier cours est terminé.
                while (isset($colonnes[$index]) && $colonnes[$index] > $evenement['debut']->getTimestamp()) {
                    $index++;
                }

                $colonnes[$index] = $evenement['fin']->getTimestamp();
                $evenement['colonne'] = $index;
                $paquetPlaces[] = $evenement;
            }

            $nbColonnes = count($colonnes);

            foreach ($paquetPlaces as $evenement) {
                $debutMinutes = ((int) $evenement['debut']->format('H') - $heureMin) * 60 + (int) $evenement['debut']->format('i');
                $duree = max(15, $evenement['debut']->diffInMinutes($evenement['fin']));

                $places[] = [
                    ...$evenement,
                    'colonnes' => $nbColonnes,
                    'haut' => round($debutMinutes / ($amplitude * 60) * 100, 3),
                    'hauteur' => round($duree / ($amplitude * 60) * 100, 3),
                ];
            }

            $paquet = [];
        };

        foreach ($duJour as $evenement) {
            // Nouveau paquet dès qu'un créneau commence après la fin de tous
            // les précédents : plus aucun chevauchement à arbitrer.
            if ($finPaquet !== null && $evenement['debut']->getTimestamp() >= $finPaquet) {
                $vider();
                $finPaquet = null;
            }

            $paquet[] = $evenement;
            $finPaquet = max($finPaquet ?? 0, $evenement['fin']->getTimestamp());
        }

        if ($paquet) {
            $vider();
        }

        return $places;
    }
}
