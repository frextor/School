<?php

namespace App\Http\Controllers;

use App\Models\ReunionInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/**
 * Portage de `Archivesreunion.php` (CodeIgniter) — liste des réunions
 * d'information passées, avec taux de remplissage / présence.
 *
 * NON couvert : `reunions()` (page HTML avec formulaire de filtre par
 * formation et liste des contacts sans réunion) — dépend du module Contacts,
 * migré séparément. Seul l'équivalent JSON de `get_reunions()` est porté ici.
 */
class ArchivesReunionController extends Controller
{
    public function index(): JsonResponse
    {
        $reunions = ReunionInformation::query()
            ->with(['formations', 'inscriptions', 'inscriptionsPresentes'])
            ->passees()
            ->get()
            ->map(function (ReunionInformation $reunion) {
                $effectifInscrit = $reunion->inscriptions->count();
                $effectifPresent = $reunion->inscriptionsPresentes->count();
                $complet = $effectifInscrit >= $reunion->effectif;

                return [
                    'id_reunion_information' => $reunion->id_reunion_information,
                    'niveaux' => $reunion->formations->pluck('niveau')->all(),
                    'date' => ucfirst(Carbon::parse($reunion->date)->locale('fr')->isoFormat('dddd DD MMMM YYYY [à] HH[h]mm')),
                    'distanciel' => (bool) $reunion->distanciel,
                    'lieu' => mb_strtoupper($reunion->lieu),
                    'effectif_inscrit' => $effectifInscrit,
                    'effectif_max' => $reunion->effectif,
                    'complet' => $complet,
                    'effectif_present' => $effectifPresent,
                ];
            });

        return response()->json(['data' => $reunions]);
    }
}
