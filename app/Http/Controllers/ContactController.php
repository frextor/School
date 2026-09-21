<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactEcole;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\ReunionInformation;
use App\Models\ReunionInformationContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Portage de `Contacts.php` (CodeIgniter) — cœur du CRM (fiche contact).
 *
 * Simplifications et exclusions assumées :
 * - Les 4 exports CSV quasi identiques (`export`, `export_agent`, `export_salon`,
 *   `export_no_reunion`, `export_base`) ne sont pas portés dans ce premier
 *   passage : ce sont des rapports, pas une fonctionnalité structurante.
 * - Les listes spécialisées `liste_agent()` / `liste_salon()` (variantes de
 *   la liste principale filtrées sur `agent_de_joueur`/`salon`) ne sont pas
 *   dupliquées : `index()` accepte un paramètre de filtre équivalent.
 * - **Aucun appel Mautic** (`mautic_api_model->confirm_ri`) — explicitement
 *   exclu à la demande de l'utilisateur.
 * - `set_trace_contact()` / `set_trace_admin()` (journalisation CRM) non
 *   portés : dépendent d'un module de journal d'activité non migré.
 * - La mise à jour du compte "espace élève" (`update users.username`) lors
 *   du changement d'email n'est pas répliquée : dépend du module User/espace
 *   élève, non migré.
 */
class ContactController extends Controller
{
    /** Portage réduit de `Crm.php::base()` : mêmes données (`amos_contacts`) que la fiche
     *  contact simple, avec segments + filtres avancés + tri, plutôt que les ~25 filtres du
     *  legacy (dont plusieurs liés à Mautic, exclu — voir MIGRATION_PROGRESS.md). */
    public function index(Request $request): View|StreamedResponse
    {
        // Colonnes triables : formation/origine/statut passent par jointure ou expression
        // dérivée (même logique que celle affichée dans la vue) pour que le tri soit réel.
        $origineSql = "CASE
            WHEN EXISTS (SELECT 1 FROM amos_reunions_information_contacts ric WHERE ric.id_contact = amos_contacts.id_contact) THEN 'Réunion d''info'
            WHEN amos_contacts.salon = 1 THEN 'Salon'
            WHEN amos_contacts.agent_de_joueur = 1 THEN 'Partenaire'
            ELSE 'Site web'
        END";
        $statutSql = "CASE amos_eleves.profil
            WHEN 'eleve' THEN 'Élève' WHEN 'reinscrit' THEN 'Réinscrit' WHEN 'alumni' THEN 'Alumni'
            WHEN 'abandon' THEN 'Abandon' WHEN 'candidat' THEN 'Candidat' ELSE 'Prospect'
        END";
        $colonnesTriables = [
            'nom' => 'amos_contacts.nom',
            'telephone' => 'amos_contacts.telephone',
            'ville' => 'amos_contacts.ville',
            'formation' => 'amos_formations.niveau',
            'origine' => DB::raw($origineSql),
            'statut' => DB::raw($statutSql),
        ];
        $tri = $colonnesTriables[$request->string('tri')->toString()] ?? null;
        $sens = $request->string('sens') === 'asc' ? 'asc' : 'desc';

        $segment = $request->string('segment')->toString() ?: 'tous';

        $base = Contact::query()
            ->select('amos_contacts.*')
            ->leftJoin('amos_formations', 'amos_formations.id_formation', '=', 'amos_contacts.id_formation')
            ->leftJoin('amos_eleves', 'amos_eleves.id_contact', '=', 'amos_contacts.id_contact')
            ->with(['eleve', 'formation', 'ecoles', 'inscriptionsReunion.reunion'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where(function ($q) use ($terme) {
                    $q->where('amos_contacts.nom', 'like', "%{$terme}%")
                        ->orWhere('amos_contacts.prenom', 'like', "%{$terme}%")
                        ->orWhere('amos_contacts.email', 'like', "%{$terme}%")
                        ->orWhere('amos_contacts.telephone', 'like', "%{$terme}%");
                });
            })
            ->when($request->filled('id_formation'), fn ($q) => $q->where('amos_contacts.id_formation', $request->integer('id_formation')))
            ->when($request->filled('etablissement'), fn ($q) => $q->whereHas('ecoles', fn ($q) => $q->where('etablissement', $request->string('etablissement'))))
            ->when($request->filled('ville'), fn ($q) => $q->where('amos_contacts.ville', 'like', '%'.$request->string('ville').'%'))
            ->when($request->filled('code_postal'), fn ($q) => $q->where('amos_contacts.code_postal', 'like', $request->string('code_postal').'%'))
            ->when($request->filled('id_reunion_information'), fn ($q) => $q->whereHas('inscriptionsReunion', fn ($q) => $q->where('id_reunion_information', $request->integer('id_reunion_information'))))
            ->when($request->filled('est_eleve'), fn ($q) => $request->boolean('est_eleve') ? $q->has('eleve') : $q->doesntHave('eleve'))
            ->when($request->boolean('agent_de_joueur'), fn ($q) => $q->where('amos_contacts.agent_de_joueur', 1))
            ->when($request->boolean('salon'), fn ($q) => $q->where('amos_contacts.salon', 1))
            ->when($request->boolean('newsletter'), fn ($q) => $q->where('amos_contacts.newsletter', 1))
            ->when($request->boolean('offres_partenaires'), fn ($q) => $q->where('amos_contacts.offres_partenaires', 1))
            ->when($request->boolean('stop_relances'), fn ($q) => $q->where('amos_contacts.stop_relances', 1));

        $segments = [
            'prospects' => fn ($q) => $q->doesntHave('eleve'),
            'candidats' => fn ($q) => $q->whereHas('eleve', fn ($q) => $q->where('profil', 'candidat')),
            'agents' => fn ($q) => $q->where('amos_contacts.agent_de_joueur', 1),
            'salons' => fn ($q) => $q->where('amos_contacts.salon', 1),
            'stop' => fn ($q) => $q->where('amos_contacts.stop_relances', 1),
        ];

        // Comptes affichés sur chaque onglet de segment, calculés sur la même base filtrée
        // (hors segment lui-même) pour rester cohérents avec les filtres actifs.
        $comptesSegments = ['tous' => (clone $base)->count()];
        foreach ($segments as $cle => $portee) {
            $comptesSegments[$cle] = $portee(clone $base)->count();
        }

        if (isset($segments[$segment])) {
            $base = $segments[$segment]($base);
        }

        if ($request->boolean('export')) {
            return $this->exporterCsv((clone $base)->orderByDesc('amos_contacts.id_contact')->get());
        }

        $contacts = $base
            ->when($tri, fn ($q) => $q->orderBy($tri, $sens), fn ($q) => $q->orderByDesc('amos_contacts.id_contact'))
            ->paginate(25)
            ->withQueryString();

        return view('contacts.index', [
            'contacts' => $contacts,
            'filtres' => $request->only(['recherche', 'agent_de_joueur', 'salon', 'newsletter', 'offres_partenaires', 'stop_relances']),
            'comptesSegments' => $comptesSegments,
            'formations' => Formation::orderBy('niveau')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'reunions' => ReunionInformation::orderByDesc('date')->limit(50)->get(),
        ]);
    }

    private function exporterCsv($contacts): StreamedResponse
    {
        return response()->streamDownload(function () use ($contacts) {
            $sortie = fopen('php://output', 'w');
            fputcsv($sortie, ['Civilité', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Ville', 'Code postal', 'Formation'], ';');

            foreach ($contacts as $contact) {
                fputcsv($sortie, [
                    $contact->civilite,
                    $contact->nom,
                    $contact->prenom,
                    $contact->email,
                    $contact->telephone,
                    $contact->ville,
                    $contact->code_postal,
                    $contact->formation?->niveau ?? '',
                ], ';');
            }

            fclose($sortie);
        }, 'contacts-'.date('Ymd_Hi').'.csv', ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    public function show(Contact $contact): View
    {
        $contact->load(['eleve', 'formation', 'ecoles', 'inscriptionsReunion.reunion', 'annotations']);

        return view('contacts.show', ['contact' => $contact]);
    }

    public function edit(Contact $contact): View
    {
        $contact->load('ecoles');

        return view('contacts.edit', [
            'contact' => $contact,
            'formations' => Formation::orderBy('niveau')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'reunions' => ReunionInformation::aVenir()->get(),
        ]);
    }

    /** Portage de `update_contact()` (sans les intégrations Mautic/journal — cf. en-tête de classe). */
    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $data = $request->validate([
            'civilite' => ['required', 'in:M,Mme,Melle'],
            'nom' => ['required', 'string', 'max:30'],
            'prenom' => ['required', 'string', 'max:30'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:60'],
            'adresse' => ['nullable', 'string', 'max:100'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:30'],
            'pays' => ['nullable', 'string', 'max:30'],
            // État civil : saisi par l'école à l'inscription (dossier Massar),
            // il n'était ni modifiable ni affiché nulle part.
            'sexe' => ['nullable', 'in:m,f'],
            'date_naissance' => ['nullable', 'date'],
            'lieu_naissance' => ['nullable', 'string', 'max:25'],
            'pays_naissance' => ['nullable', 'string', 'max:20'],
            'nationalite' => ['nullable', 'string', 'max:20'],
            'id_formation' => ['nullable', 'integer', 'exists:amos_formations,id_formation'],
            'id_reunion_information' => ['nullable', 'integer', 'exists:amos_reunions_information,id_reunion_information'],
            'newsletter' => ['boolean'],
            'offres_partenaires' => ['boolean'],
            'intitule_derniere_formation' => ['nullable', 'string', 'max:80'],
            'niveau_derniere_formation' => ['nullable', 'string', 'max:10'],
            'lieu_derniere_formation' => ['nullable', 'string', 'max:60'],
            'date_derniere_formation' => ['nullable', 'string', 'max:7'],
            'diplome_derniere_formation' => ['nullable', 'string', 'max:80'],
            'annotation' => ['nullable', 'string'],
            'annee_rentree' => ['nullable', 'integer'],
            'etablissements' => ['array'],
            'etablissements.*' => ['string', 'max:80'],
        ]);

        DB::transaction(function () use ($data, $contact, $request) {
            $contact->update([
                'civilite' => $data['civilite'],
                'nom' => ucfirst($data['nom']),
                'prenom' => ucfirst($data['prenom']),
                'telephone' => $data['telephone'] ?? '',
                'email' => strtolower($data['email']),
                'adresse' => $data['adresse'] ?? '',
                'code_postal' => $data['code_postal'] ?? '',
                'ville' => $data['ville'] ?? '',
                'pays' => $data['pays'] ?? '',
                'date_naissance' => $data['date_naissance'] ?? '',
                'lieu_naissance' => $data['lieu_naissance'] ?? '',
                'pays_naissance' => $data['pays_naissance'] ?? '',
                'nationalite' => $data['nationalite'] ?? '',
                'id_formation' => $data['id_formation'] ?? 0,
                'newsletter' => $request->boolean('newsletter'),
                'offres_partenaires' => $request->boolean('offres_partenaires'),
                'intitule_derniere_formation' => $data['intitule_derniere_formation'] ?? '',
                'niveau_derniere_formation' => $data['niveau_derniere_formation'] ?? '',
                'lieu_derniere_formation' => $data['lieu_derniere_formation'] ?? '',
                'date_derniere_formation' => $data['date_derniere_formation'] ?? '',
                'diplome_derniere_formation' => $data['diplome_derniere_formation'] ?? '',
                'annotation' => $data['annotation'] ?? '',
                'annee_rentree' => $data['annee_rentree'] ?? 0,
            ]);

            // `sexe` est un enum('f','m') NOT NULL : MySQL refuse la chaîne
            // vide, on ne touche donc à la colonne que si le champ est rempli.
            if (filled($data['sexe'] ?? null)) {
                $contact->update(['sexe' => $data['sexe']]);
            }

            $contact->ecoles()->delete();
            foreach ($data['etablissements'] ?? [] as $ville) {
                ContactEcole::create([
                    'id_contact' => $contact->id_contact,
                    'etablissement' => $ville,
                    'ordre' => $request->input("ordre_{$ville}", ''),
                ]);
            }

            $contact->inscriptionsReunion()->delete();
            if ($data['id_reunion_information'] ?? null) {
                ReunionInformationContact::create([
                    'id_reunion_information' => $data['id_reunion_information'],
                    'id_contact' => $contact->id_contact,
                    'presence' => false,
                    'rappel' => false,
                ]);
            }
        });

        return redirect()
            ->route('contacts.show', $contact)
            ->with('status', 'Fiche contact mise à jour.');
    }

    /** Portage de `modifier()` — mise à jour en ligne d'un champ unique (x-editable). */
    public function updateField(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pk' => ['required', 'integer', 'exists:amos_contacts,id_contact'],
            'name' => ['required', 'string', 'in:nom,prenom,telephone,email,ville,code_postal,annotation'],
            'value' => ['nullable', 'string'],
        ]);

        Contact::where('id_contact', $data['pk'])->update([$data['name'] => $data['value']]);

        return response()->json('OK');
    }

    /** Portage de `supprimer()` — suppression interdite si le contact est devenu élève. */
    public function destroy(Contact $contact): JsonResponse|RedirectResponse
    {
        if ($contact->eleve) {
            return request()->wantsJson()
                ? response()->json('ko')
                : back()->withErrors(['contact' => 'Ce contact est un élève, il ne peut pas être supprimé.']);
        }

        DB::transaction(function () use ($contact) {
            $contact->ecoles()->delete();
            $contact->inscriptionsReunion()->delete();
            $contact->delete();
        });

        return request()->wantsJson() ? response()->json('ok') : redirect()->route('contacts.index');
    }

    /** Portage de `archiver()`. */
    public function archive(Contact $contact): JsonResponse
    {
        $contact->update(['visible' => true]);

        return response()->json('ok');
    }

    /** Portage de `autoriser_relances()`. */
    public function allowRelances(Contact $contact): JsonResponse
    {
        $contact->update(['stop_relances' => false]);

        return response()->json('ok');
    }
}
