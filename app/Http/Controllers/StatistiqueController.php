<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Portage de `Statistiques.php` + `helpers/contacts_helper.php` (getcandidatbyetablissement,
 * getcontactbyetablissement) + `helpers/eleves_helper.php` (getelevebyetablissement,
 * getelevebyniveau).
 */
class StatistiqueController extends Controller
{
    public function contactsByEtablissement(): JsonResponse
    {
        $data = DB::table('amos_contact_ecoles')
            ->select('etablissement', DB::raw('count(*) as count'))
            ->groupBy('etablissement')
            ->get();

        return response()->json($data);
    }

    public function candidatsByEtablissement(): JsonResponse
    {
        $data = DB::table('amos_eleves as ae')
            ->join('amos_contact_ecoles as ac', 'ae.id_eleve', '=', 'ac.id_contact')
            ->where('ae.profil', Eleve::PROFIL_CANDIDAT)
            ->select('ac.etablissement', DB::raw('count(*) as count'))
            ->groupBy('ac.etablissement')
            ->get();

        return response()->json($data);
    }

    public function elevesByEtablissement(): JsonResponse
    {
        $data = DB::table('amos_eleves as ae')
            ->join('amos_contact_ecoles as ac', 'ae.id_eleve', '=', 'ac.id_contact')
            ->where('ae.profil', Eleve::PROFIL_ELEVE)
            ->select('ac.etablissement', DB::raw('count(*) as count'))
            ->groupBy('ac.etablissement')
            ->get();

        return response()->json($data);
    }

    public function elevesByNiveau(): JsonResponse
    {
        $data = DB::table('amos_niveaux as a')
            ->join('amos_eleves as e', 'a.id_niveau', '=', 'e.id_niveau')
            ->select('a.nom_niveau', DB::raw('count(e.id_eleve) as count'))
            ->groupBy('e.id_niveau', 'a.nom_niveau')
            ->get();

        return response()->json($data);
    }
}
