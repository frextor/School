<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ReunionInformationContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Portage des formulaires publics du site vitrine (`Formulaires.php`,
 * CodeIgniter) — pas d'authentification requise, comme le legacy.
 *
 * Simplifications et exclusions assumées (cohérentes avec ImportController) :
 * - **Aucun envoi d'email** (confirmation, brochure) ni intégration Mautic.
 * - Pas de création automatique de fiche Élève + compte "espace élève"
 *   (`save_eleve`/`save_user`) : reste un geste manuel via le module Eleves.
 * - Pas de journalisation CRM (`set_trace_contact`).
 * - Seuls `contact()` (formulaire de contact / inscription réunion) et
 *   `demande_brochure()` sont portés ; les formulaires de candidature
 *   multi-étapes (`new_candidat_save_steps`, `candidat_amos`,
 *   `eleve_importe`...) et l'upload de pièces jointes (`add_files`) restent
 *   à traiter séparément (gros formulaires "wizard", ~1500 lignes).
 */
class PublicFormController extends Controller
{
    /** Portage de `contact()`. */
    public function storeContact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'civilite' => ['required', 'in:M,Mme,Melle'],
            'nom' => ['required', 'string', 'max:30'],
            'prenom' => ['required', 'string', 'max:30'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:60'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:30'],
            'id_formation' => ['nullable', 'integer'],
            'id_reunion_information' => ['nullable'],
            'newsletter' => ['boolean'],
            'offres_partenaires' => ['boolean'],
            'annee_rentree' => ['nullable', 'integer'],
        ]);

        $contact = Contact::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                ...$this->champsObligatoiresParDefaut(),
                'civilite' => $data['civilite'],
                'nom' => ucfirst($data['nom']),
                'prenom' => ucfirst($data['prenom']),
                'sexe' => $data['civilite'] === 'M' ? 'm' : 'f',
                'telephone' => $data['telephone'] ?? '',
                'code_postal' => $data['code_postal'] ?? '',
                'ville' => $data['ville'] ?? '',
                'id_formation' => $data['id_formation'] ?? 0,
                'newsletter' => $request->boolean('newsletter'),
                'offres_partenaires' => $request->boolean('offres_partenaires'),
                'annee_rentree' => $data['annee_rentree'] ?? now()->year,
                'date_inscription' => now(),
            ]
        );

        if ($contact->wasRecentlyCreated) {
            $contact->update(['id_contact_parent' => $contact->id_contact]);
        }

        if (! empty($data['id_reunion_information']) && $data['id_reunion_information'] !== 'autres') {
            ReunionInformationContact::updateOrCreate(
                ['id_contact' => $contact->id_contact, 'id_reunion_information' => $data['id_reunion_information']],
                ['presence' => false, 'rappel' => false]
            );
        }

        return response()->json(['status' => 'success', 'id_contact' => $contact->id_contact]);
    }

    /** Portage de `demande_brochure_handle_form_data()`. */
    public function storeBrochureRequest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'civilite' => ['nullable', 'in:M,Mme,Melle'],
            'nom' => ['required', 'string', 'max:30'],
            'prenom' => ['required', 'string', 'max:30'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:60'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:30'],
            'pays' => ['nullable', 'string', 'max:30'],
            'id_formation' => ['nullable', 'integer'],
            'newsletter' => ['boolean'],
            'offres_partenaires' => ['boolean'],
            'annee_rentree' => ['nullable', 'integer'],
        ]);

        $contact = Contact::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                ...$this->champsObligatoiresParDefaut(),
                'civilite' => $data['civilite'] ?? 'M',
                'nom' => ucfirst($data['nom']),
                'prenom' => ucfirst($data['prenom']),
                'sexe' => ($data['civilite'] ?? 'M') === 'M' ? 'm' : 'f',
                'telephone' => $data['telephone'] ?? '',
                'code_postal' => $data['code_postal'] ?? '',
                'ville' => $data['ville'] ?? '',
                'pays' => $data['pays'] ?? '',
                'id_formation' => $data['id_formation'] ?? 0,
                'newsletter' => $request->boolean('newsletter'),
                'offres_partenaires' => $request->boolean('offres_partenaires'),
                'annee_rentree' => $data['annee_rentree'] ?? now()->year,
                'demande_brochure' => true,
                'reunion_info' => true,
                'source' => 1,
                'date_inscription' => now(),
            ]
        );

        if ($contact->wasRecentlyCreated) {
            $contact->update(['id_contact_parent' => $contact->id_contact]);
        }

        return response()->json(['status' => 'success', 'id_contact' => $contact->id_contact]);
    }

    /**
     * Valeurs par défaut pour les colonnes `amos_contacts` NOT NULL sans
     * défaut MySQL, non renseignées par ces formulaires publics (même liste
     * que `ImportController`).
     */
    private function champsObligatoiresParDefaut(): array
    {
        return [
            'id_contact_parent' => 0,
            'date_naissance' => '',
            'lieu_naissance' => '',
            'nationalite' => '',
            'adresse' => '',
            'email_office' => '',
            'pays' => '',
            'comment_connaitre_amos' => '',
            'intitule_derniere_formation' => '',
            'lieu_derniere_formation' => '',
            'date_derniere_formation' => '',
            'niveau_derniere_formation' => '',
            'diplome_derniere_formation' => '',
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
            'visible' => false,
            'salon_nom' => '',
            'salon_ville' => '',
            'salon_date' => now(),
            'annotation' => '',
            'step' => '1',
            'suivre_actu' => false,
            'source' => 0,
        ];
    }
}
