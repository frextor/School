<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Contact;
use App\Models\Eleve;
use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage Laravel du controller CodeIgniter `Eleves.php` / `Eleves_model.php`.
 * Ne couvre pour l'instant que le CRUD de base (liste, fiche, création,
 * mise à jour, suppression) — les fonctionnalités annexes (paiements,
 * factures, épreuves d'admission, exports...) seront migrées séparément
 * module par module.
 */
class EleveController extends Controller
{
    public function index(Request $request): View
    {
        $eleves = Eleve::query()
            ->with(['contact', 'niveau', 'classe'])
            // Les "candidats" (profil = candidat) ont leur propre écran dédié
            // (CandidatController) — la liste des élèves ne doit jamais les
            // afficher, même si un filtre profil est demandé.
            ->where('profil', '!=', Eleve::PROFIL_CANDIDAT)
            ->when($request->filled('profil'), fn ($q) => $q->where('profil', $request->string('profil')))
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->whereHas('contact', function ($q) use ($terme) {
                    $q->where('nom', 'like', "%{$terme}%")
                        ->orWhere('prenom', 'like', "%{$terme}%");
                });
            })
            // Portage de `Archives.php::eleves()` : bascule liste active / archivée
            // plutôt qu'un écran dupliqué (voir MIGRATION_PROGRESS.md).
            ->where('visible', $request->boolean('archives'))
            ->orderByDesc('id_eleve')
            ->paginate(25)
            ->withQueryString();

        return view('eleves.index', [
            'eleves' => $eleves,
            'filtres' => $request->only(['profil', 'recherche', 'archives']),
        ]);
    }

    public function show(Eleve $eleve): View
    {
        $eleve->load(['contact', 'niveau', 'niveauFuture', 'classe']);

        return view('eleves.show', ['eleve' => $eleve]);
    }

    public function create(): View
    {
        return view('eleves.create', [
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(),
            // Portage de add_eleve() : plutôt qu'un ID de contact saisi à la main,
            // l'admin choisit un contact déjà existant (fiche CRM) dans une liste,
            // ou laisse ce champ vide pour que la fiche contact soit créée
            // automatiquement à partir des champs nom/prénom/email ci-dessous.
            'contacts' => Contact::orderBy('nom')->orderBy('prenom')->get(['id_contact', 'nom', 'prenom', 'email']),
        ]);
    }

    /** Portage de `Eleves.php::add_eleve()` : la fiche contact est liée ou créée automatiquement, jamais saisie par ID. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_contact' => ['nullable', 'integer', 'exists:amos_contacts,id_contact'],
            'civilite' => ['required_without:id_contact', 'nullable', 'in:M,Mme,Melle'],
            'nom' => ['required_without:id_contact', 'nullable', 'string', 'max:30'],
            'prenom' => ['required_without:id_contact', 'nullable', 'string', 'max:30'],
            'email' => ['required_without:id_contact', 'nullable', 'email', 'max:60'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'id_niveau' => ['required', 'integer'],
            'id_classe' => ['nullable', 'integer'],
            'profil' => ['required', 'in:eleve,alumni,reinscrit,abandon'],
            'date_inscription' => ['nullable', 'date'],
            'montant_formation' => ['nullable', 'string', 'max:20'],
        ]);

        $eleve = DB::transaction(function () use ($data) {
            if (! empty($data['id_contact'])) {
                $idContact = $data['id_contact'];
            } else {
                $contact = Contact::create([
                    ...$this->contactChampsObligatoiresParDefaut(),
                    'civilite' => $data['civilite'],
                    'nom' => ucfirst($data['nom']),
                    'prenom' => ucfirst($data['prenom']),
                    'sexe' => $data['civilite'] === 'M' ? 'm' : 'f',
                    'email' => strtolower($data['email']),
                    'telephone' => $data['telephone'] ?? '',
                    'date_inscription' => now(),
                ]);
                $contact->update(['id_contact_parent' => $contact->id_contact]);
                $idContact = $contact->id_contact;
            }

            $eleve = Eleve::create([
                ...$this->eleveChampsObligatoiresParDefaut(),
                'id_contact' => $idContact,
                'id_niveau' => $data['id_niveau'],
                'id_classe' => $data['id_classe'] ?? 0,
                'profil' => $data['profil'],
                'date_inscription' => $data['date_inscription'] ?? now(),
                'montant_formation' => $data['montant_formation'] ?? '',
            ]);

            // Portage de `add_eleve()` : l'élève est son propre "parent" par défaut
            // (id_eleve_parent auto-référencé), comme pour id_contact_parent côté contact.
            $eleve->update(['id_eleve_parent' => $eleve->id_eleve]);

            return $eleve;
        });

        return redirect()
            ->route('eleves.show', $eleve)
            ->with('status', 'Élève créé avec succès.');
    }

    /** Valeurs par défaut pour les colonnes `amos_eleves` NOT NULL sans défaut MySQL. */
    private function eleveChampsObligatoiresParDefaut(): array
    {
        return [
            'id_eleve_parent' => 0,
            'lang_maternelle' => '',
            'situation_famille' => '',
            'avoir_enfants' => 'non',
            'nbr_enfants' => '',
            'situation_actuelle' => '',
            'bac_obtenu_encours' => '',
            'situation_actuelle_autre' => '',
            'photo' => '',
            'formations_complementaires' => '',
            'duree_experience_pro' => '',
            'unite_experience_pro' => 'mois',
            'motivations' => '',
            'competences' => '',
            'autres_competences' => '',
            'qualites' => '',
            'defauts' => '',
            'commentaire' => '',
            'carte_identite' => '',
            'diplomes' => '',
            'releves_notes' => '',
            'cv' => '',
            'lettre_motivation' => '',
            'date_depot' => now(),
            'id_niveau_future' => 0,
            'valide' => false,
            'rappel_paiement' => false,
            'dernier_epreuve' => 0,
            'compteur_epreuve' => false,
            'reaffectation_manuelle_epreuve' => false,
            'rappel_epreuve' => false,
            'echelonnement' => false,
            'presence_eleve' => false,
            'visible' => true,
            'paiement_valide' => false,
            'attente_epreuve' => false,
            'visible_attente_epreuve' => false,
            'importe' => false,
            'numero_social' => '',
            'cacher_encaissement' => false,
            'num_facture' => '',
            'groupes' => '',
            'specialisations' => '',
        ];
    }

    /** Valeurs par défaut pour les colonnes `amos_contacts` NOT NULL sans défaut (même liste que PublicFormController/ImportController). */
    private function contactChampsObligatoiresParDefaut(): array
    {
        return [
            'id_contact_parent' => 0,
            'date_naissance' => '',
            'lieu_naissance' => '',
            'nationalite' => '',
            'adresse' => '',
            'email_office' => '',
            'code_postal' => '',
            'ville' => '',
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
            'reunion_info' => false,
            'derniere_reunion' => 0,
            'compteur_reunion' => false,
            'reaffectation_manuelle' => false,
            'demande_brochure' => false,
            'rappel_reunion' => false,
            'participe_reunion' => false,
            'salon' => false,
            'agent_de_joueur' => false,
            'visible' => true,
            'salon_nom' => '',
            'salon_ville' => '',
            'salon_date' => now(),
            'annotation' => '',
            'step' => '1',
            'annee_rentree' => now()->year,
            'source' => 0,
            'suivre_actu' => false,
        ];
    }

    public function edit(Eleve $eleve): View
    {
        return view('eleves.edit', [
            'eleve' => $eleve,
            'niveaux' => Niveau::orderBy('nom_niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(),
        ]);
    }

    public function update(Request $request, Eleve $eleve): RedirectResponse
    {
        $data = $request->validate([
            'id_niveau' => ['required', 'integer'],
            'id_classe' => ['nullable', 'integer'],
            'profil' => ['required', 'in:eleve,alumni,reinscrit,abandon'],
            'valide' => ['boolean'],
            'visible' => ['boolean'],
            'montant_formation' => ['nullable', 'string', 'max:20'],
            'commentaire' => ['nullable', 'string'],
        ]);

        $eleve->update($data);

        return redirect()
            ->route('eleves.show', $eleve)
            ->with('status', 'Élève mis à jour.');
    }

    public function destroy(Eleve $eleve): RedirectResponse
    {
        // Le legacy ne supprime jamais réellement une fiche élève : on
        // reproduit ce comportement en masquant plutôt qu'en détruisant.
        $eleve->update(['visible' => false]);

        return redirect()
            ->route('eleves.index')
            ->with('status', 'Élève masqué.');
    }
}
