<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactEcole;
use App\Models\ContactSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Portage de `Import.php` (CodeIgniter) — import en masse de contacts
 * depuis un fichier CSV/Excel (fiche "nom, prénom, téléphone, email...").
 *
 * Portage de `import_data()` (la méthode active ; `import_data_old()` est un
 * code mort du legacy — jamais appelé depuis les vues — non porté).
 *
 * Simplifications et exclusions assumées :
 * - Lecture via PhpOffice/PhpSpreadsheet (CSV **et** Excel), alors que le
 *   legacy actif ne lisait que du CSV via `fgetcsv()` — améliore la
 *   couverture sans rien retirer.
 * - **Aucun appel Mautic** (`add_contact` vers l'API Mautic, déjà commenté
 *   dans le legacy) — explicitement exclu à la demande de l'utilisateur.
 * - La création automatique d'une fiche Élève + d'un compte "espace élève"
 *   (`add_eleve()`, `save_user()`) pour chaque contact importé n'est **pas**
 *   reproduite : ce sont des responsabilités des modules Eleves/User, pas
 *   encore migrées sous une forme réutilisable ici. L'import crée la fiche
 *   Contact (+ son établissement candidaté) ; la conversion en élève reste
 *   un geste manuel via le module Eleves.
 */
class ImportController extends Controller
{
    private const COLONNES_ATTENDUES = ['Civilite', 'Nom', 'Prenom', 'Telephone', 'Email', 'Code_postal', 'Ville', 'Annee_rentree', 'Ecole'];

    public function index(): View
    {
        return view('import.index', ['sources' => ContactSource::orderBy('titre')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'upload_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
            'source' => ['nullable', 'integer', 'exists:amos_contacts_sources,id_source'],
        ]);

        $fichier = $request->file('upload_file');
        $extension = strtolower($fichier->getClientOriginalExtension());

        $reader = $extension === 'csv' || $extension === 'txt'
            ? IOFactory::createReader('Csv')
            : IOFactory::createReader('Xlsx');

        if (method_exists($reader, 'setDelimiter')) {
            $reader->setDelimiter(';');
        }

        $lignes = $reader->load($fichier->getRealPath())->getActiveSheet()->toArray();

        if (empty($lignes)) {
            return back()->withErrors(['upload_file' => 'Le fichier est vide.']);
        }

        $entetes = array_map('trim', array_shift($lignes));
        $manquantes = array_diff(self::COLONNES_ATTENDUES, $entetes);

        if ($manquantes) {
            return back()->withErrors(['upload_file' => "Colonnes manquantes dans l'en-tête : ".implode(', ', $manquantes)]);
        }

        $importes = 0;
        $ignores = 0;

        DB::transaction(function () use ($lignes, $entetes, $request, &$importes, &$ignores) {
            foreach ($lignes as $ligne) {
                $donnees = @array_combine($entetes, $ligne);

                if (! $donnees || empty($donnees['Telephone']) || empty($donnees['Annee_rentree']) || $this->emailDejaUtilise($donnees['Email'] ?? '')) {
                    $ignores++;

                    continue;
                }

                $contact = Contact::create([
                    'id_contact_parent' => 0,
                    'civilite' => $donnees['Civilite'] ?: 'M',
                    'nom' => ucfirst($donnees['Nom'] ?? ''),
                    'prenom' => ucfirst($donnees['Prenom'] ?? ''),
                    'sexe' => ($donnees['Civilite'] ?? 'M') === 'M' ? 'm' : 'f',
                    'date_naissance' => '',
                    'lieu_naissance' => '',
                    'nationalite' => '',
                    'adresse' => '',
                    'telephone' => $donnees['Telephone'] ?? '',
                    'email' => strtolower($donnees['Email'] ?? ''),
                    'email_office' => '',
                    'code_postal' => $donnees['Code_postal'] ?? '',
                    'ville' => $donnees['Ville'] ?? '',
                    'pays' => '',
                    'id_formation' => 0,
                    'comment_connaitre_amos' => '',
                    'intitule_derniere_formation' => '',
                    'lieu_derniere_formation' => '',
                    'date_derniere_formation' => '',
                    'niveau_derniere_formation' => '',
                    'diplome_derniere_formation' => '',
                    'newsletter' => false,
                    'offres_partenaires' => false,
                    'candidat' => false,
                    'annee_rentree' => $donnees['Annee_rentree'] ?? '',
                    'source' => $request->integer('source'),
                    'date_inscription' => now(),
                    'reunion_info' => true,
                    'derniere_reunion' => 0,
                    'compteur_reunion' => false,
                    'reaffectation_manuelle' => false,
                    'demande_brochure' => false,
                    'rappel_reunion' => false,
                    'participe_reunion' => false,
                    'salon' => false,
                    'agent_de_joueur' => false,
                    'visible' => false,
                    'salon_nom' => '',
                    'salon_ville' => '',
                    'salon_date' => now(),
                    'annotation' => '',
                    'step' => '1',
                    'suivre_actu' => false,
                ]);

                $contact->update(['id_contact_parent' => $contact->id_contact]);

                if (! empty($donnees['Ecole'])) {
                    ContactEcole::create([
                        'id_contact' => $contact->id_contact,
                        'etablissement' => $donnees['Ecole'],
                        'ordre' => '1',
                    ]);
                }

                $importes++;
            }
        });

        return redirect()
            ->route('import.index')
            ->with('status', "{$importes} contact(s) importé(s) avec succès.".($ignores ? " {$ignores} ligne(s) ignorée(s) (téléphone/année de rentrée manquant ou email déjà existant)." : ''));
    }

    private function emailDejaUtilise(string $email): bool
    {
        return $email !== '' && Contact::where('email', $email)->exists();
    }
}
