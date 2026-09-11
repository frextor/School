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
    public function index(Request $request): View
    {
        $contacts = Contact::query()
            ->with(['eleve', 'formation'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%");
            })
            ->when($request->boolean('agent_de_joueur'), fn ($q) => $q->where('agent_de_joueur', 1))
            ->when($request->boolean('salon'), fn ($q) => $q->where('salon', 1))
            ->orderByDesc('id_contact')
            ->paginate(25)
            ->withQueryString();

        return view('contacts.index', ['contacts' => $contacts, 'filtres' => $request->only(['recherche', 'agent_de_joueur', 'salon'])]);
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
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:30'],
            'pays' => ['nullable', 'string', 'max:30'],
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
                'code_postal' => $data['code_postal'] ?? '',
                'ville' => $data['ville'] ?? '',
                'pays' => $data['pays'] ?? '',
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
