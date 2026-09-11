<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Portage partiel de `Helvetius.php` : export de données vers le format
 * plat attendu par "Helvetius", un logiciel de gestion académique externe
 * utilisé par le groupe scolaire pour l'interfaçage (import étudiants /
 * enseignants). Format CSV point-virgule, encodage Latin-1 (comme l'original
 * `utf8_decode`), en-têtes de colonnes identiques au legacy.
 *
 * **Non porté** : `syllabus()`/`eleves_absences()`/`eleves_absences_details()`
 * — génération d'un fichier d'échange multi-niveaux (établissement > niveau >
 * semestre > UE > cours) avec des codes propriétaires très spécifiques au
 * format d'import Helvetius (`NV_0..NV_5`, `ctrl`, découpage caractère par
 * caractère d'un code interne, règles BACHELOR/MASTER câblées en dur). Cette
 * partie n'a de sens que si l'intégration Helvetius est toujours utilisée
 * côté établissement ; à reprendre avec un besoin confirmé plutôt que
 * reconstruite à l'aveugle.
 */
class HelvetiusExportController extends Controller
{
    public function eleves(Request $request): StreamedResponse|Response
    {
        $annee = $request->integer('annee') ?: (int) date('Y');
        $format = $request->string('format')->toString() === 'json' ? 'json' : 'csv';

        $eleves = DB::table('amos_contacts as c')
            ->leftJoin('amos_eleves as e', 'c.id_contact', '=', 'e.id_contact')
            ->leftJoin('amos_contact_ecoles as ce', function ($join) {
                $join->on('ce.id_contact', '=', 'c.id_contact')->where('ce.ordre', 1);
            })
            ->leftJoin('amos_classe as cl', 'cl.id_classe', '=', 'e.id_classe')
            ->where('e.profil', 'eleve')
            ->where('c.annee_rentree', $annee)
            ->select([
                'c.annee_rentree as annee',
                'cl.classe as classe',
                'c.civilite',
                'c.nom',
                'c.prenom',
                'c.sexe',
                'e.situation_famille',
                'c.date_naissance',
                'c.nationalite',
                'c.adresse',
                'c.code_postal',
                'c.ville',
                'c.pays',
                'c.telephone',
                'c.email',
                'ce.etablissement',
                'c.id_contact',
            ])
            ->get();

        $genres = ['M' => 'Monsieur', 'Mme' => 'Madame', 'Mlle' => 'Madame', 'Melle' => 'Madame'];

        $lignes = $eleves->map(fn ($e) => [
            'Date_Lot' => date('Y').'0901',
            'Recrutement' => 'amos',
            'Année' => $e->annee,
            'Part(CM)' => $e->classe,
            'No Année' => $e->annee,
            'Statut Etudiant' => 'ETUDIANT',
            'Genre' => $genres[$e->civilite] ?? '-',
            'Nom' => $e->nom,
            'Prénom' => $e->prenom,
            'Sexe' => strtoupper((string) $e->sexe),
            'Situation familiale' => $e->situation_famille === 'Célibataire' ? 'CELIBATAIRE' : '-',
            'Date de Naissance' => $e->date_naissance,
            'Nationalité' => strtoupper((string) $e->nationalite),
            'Adresse' => $e->adresse,
            'Code postal' => $e->code_postal,
            'Ville' => strtoupper((string) $e->ville),
            'Pays' => strtoupper((string) $e->pays),
            'Téléphone' => $e->telephone,
            'Email' => $e->email,
            'Établissement' => strtoupper((string) $e->etablissement),
            'ID_MIRACLE' => $e->id_contact,
        ]);

        return $this->stream($lignes, 'ETUDIANTS__AMOS__'.date('Ymd_H\hi'), $format);
    }

    public function profs(Request $request): StreamedResponse|Response
    {
        $format = $request->string('format')->toString() === 'json' ? 'json' : 'csv';

        $profs = DB::table('amos_intervenant as i')
            ->leftJoin('amos_nationalites as n', 'n.id_nationalite', '=', 'i.id_nationalite')
            ->leftJoin('amos_pays as p', 'p.id_pays', '=', 'i.id_pays')
            ->leftJoin('amos_intervenant_etablissement as ie', 'ie.id_intervenant', '=', 'i.id_intervenant')
            ->leftJoin('amos_etablissement as e', 'e.id_etablissement', '=', 'ie.id_etablissement')
            ->groupBy('i.id_intervenant')
            ->select([
                'i.civilite',
                'i.nom',
                'i.prenom',
                'i.date_naissance',
                'n.fr as nationalite',
                'i.adresse',
                'i.code_postal',
                'i.ville',
                'p.fr as pays',
                'i.telephone',
                'i.email',
                'i.id_intervenant',
                DB::raw("GROUP_CONCAT(e.nom_etablissement) as etablissements"),
            ])
            ->get();

        $genres = ['M' => 'Monsieur', 'Mme' => 'Madame', 'Mlle' => 'Madame', 'Melle' => 'Madame'];

        $lignes = $profs->map(fn ($p) => [
            'Genre' => $genres[$p->civilite] ?? '-',
            'Nom' => strtoupper((string) $p->nom),
            'Prénom' => $p->prenom,
            'Titre' => $genres[$p->civilite] ?? '-',
            'Date de naissance' => $p->date_naissance,
            'Nationalité' => strtoupper((string) $p->nationalite),
            'Adresse' => $p->adresse,
            'Code postal' => $p->code_postal,
            'Ville' => strtoupper((string) $p->ville),
            'Pays' => strtoupper((string) $p->pays),
            'Téléphone' => $p->telephone,
            'Email' => $p->email,
            'Établissements' => str_replace('AMOS ', '', (string) $p->etablissements),
            'ID_MIRACLE_PROF' => $p->id_intervenant,
        ]);

        return $this->stream($lignes, 'PROFS__AMOS__'.date('Ymd_H\hi'), $format);
    }

    private function stream(\Illuminate\Support\Collection $lignes, string $nomFichier, string $format): StreamedResponse|Response
    {
        if ($format === 'json') {
            return response($lignes->values()->toJson(), 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Content-Disposition' => "attachment; filename={$nomFichier}.json",
            ]);
        }

        return response()->streamDownload(function () use ($lignes) {
            $sortie = fopen('php://output', 'w');
            if ($lignes->isNotEmpty()) {
                fputcsv($sortie, array_keys($lignes->first()), ';');
            }
            foreach ($lignes as $ligne) {
                fputcsv($sortie, $ligne, ';');
            }
            fclose($sortie);
        }, "{$nomFichier}.csv", ['Content-Type' => 'text/csv; charset=utf-8']);
    }
}
