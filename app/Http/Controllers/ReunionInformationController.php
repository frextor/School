<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\ReunionInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/**
 * Portage de `Miracle.php` (CodeIgniter) — réunions d'information à venir,
 * regroupées par lieu / niveau de formation pour affichage sur le site
 * vitrine.
 *
 * `strftime()` (utilisé par le legacy, supprimé depuis PHP 8.1) est
 * remplacé par `Carbon::isoFormat()` en locale française.
 */
class ReunionInformationController extends Controller
{
    public function reunions(): JsonResponse
    {
        $data = [];

        foreach ($this->reunionsAVenir() as $reunion) {
            $niveaux = $this->niveauxPourReunion($reunion);
            $libelle = 'Réunion '.implode(' & ', $niveaux);
            $data[$reunion->lieu][$libelle][$reunion->id_reunion_information] =
                ucfirst($this->formatDate($reunion->date, 'dddd DD/MM [à] HH[h]mm'));
        }

        return response()->json($data);
    }

    public function reunionsTableaux(): JsonResponse
    {
        $data = [];

        foreach ($this->reunionsAVenir() as $reunion) {
            $lieu = mb_strtoupper($reunion->lieu);
            $formationIds = $reunion->formations->pluck('id_formation');
            $libelleDate = ucfirst($this->formatDate($reunion->date, 'DD/MM [à] HH[h]mm'));

            if ($formationIds->intersect(Formation::IDS_BACHELOR)->isNotEmpty()) {
                $data[$lieu]['bachelor'][] = $libelleDate;
            } elseif ($formationIds->intersect(Formation::IDS_MASTER)->isNotEmpty()) {
                if (! isset($data[$lieu]['master']) || ! in_array($libelleDate, $data[$lieu]['master'], true)) {
                    $data[$lieu]['master'][] = $libelleDate;
                }
            }
        }

        return response()->json($data);
    }

    public function reunionsAgent(): JsonResponse
    {
        $data = [];

        foreach ($this->reunionsAVenir(formationId: 5) as $reunion) {
            $niveaux = $reunion->formations->pluck('niveau')->all();
            $libelle = 'Réunion '.implode(' & ', $niveaux);
            $data[$reunion->lieu][$libelle][$reunion->id_reunion_information] =
                ucfirst($this->formatDate($reunion->date, 'dddd DD/MM [à] HH[h]mm'));
        }

        return response()->json($data);
    }

    public function planningAgent(): JsonResponse
    {
        $data = [];

        foreach ($this->reunionsAVenir(formationId: 5) as $reunion) {
            $data[$reunion->lieu][] = $reunion->date->format('Y-m-d H:i:s');
        }

        return response()->json($data);
    }

    private function reunionsAVenir(?int $formationId = null)
    {
        return ReunionInformation::query()
            ->with('formations')
            ->aVenir()
            ->when($formationId, fn ($q) => $q->whereHas('formations', fn ($q) => $q->where('amos_formations.id_formation', $formationId)))
            ->get();
    }

    private function niveauxPourReunion(ReunionInformation $reunion): array
    {
        $formationIds = $reunion->formations->pluck('id_formation');
        $niveaux = $reunion->formations->whereNotIn('id_formation', Formation::IDS_BACHELOR)->pluck('niveau')->all();

        if ($formationIds->intersect(Formation::IDS_BACHELOR)->isNotEmpty()) {
            array_unshift($niveaux, 'Bachelor');
        }

        return $niveaux;
    }

    private function formatDate($date, string $format): string
    {
        return Carbon::parse($date)->locale('fr')->isoFormat($format);
    }
}
