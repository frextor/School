<?php

namespace App\Http\Controllers;

use App\Models\ChequePaiement;
use App\Models\Eleve;
use App\Models\NiveauxOptions;
use App\Models\PaiementEleve;
use App\Models\PaiementEleveOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage du volet "règlements/paiements" de `Eleves.php`
 * (add_reglements / update_reglements / delete_reglement), la plus grosse
 * partie restante du module Élèves.
 *
 * Simplifications et exclusions assumées :
 * - **Aucun appel Mautic/CRM** (`crm_library->set_profil_label`,
 *   `set_trace_contact`, `journal->watch`) — cohérent avec le reste de la
 *   migration.
 * - L'upload de la photo du chèque (`photo_cheque`) est géré via
 *   `Storage::disk('public')` (nom de fichier seul en base, comme
 *   `Intervenant`/`Signature`) plutôt que le chemin complet.
 * - Le recalcul du statut de paiement (`get_eleve_paiement_statut` — logique
 *   de rapprochement complexe entre échéancier et montant réglé) est
 *   simplifié : le statut choisi dans le formulaire (`Payé` / `Accord OPCO`
 *   / `cas particulier`) est appliqué directement, sans recalcul
 *   automatique à partir des chèques encaissés.
 * - Les factures/avoirs PDF (`add_facture_eleve`, `add_avoir_eleve`,
 *   `export_facture`, ~600 lignes) restent un chantier séparé (génération
 *   de documents comptables, hors périmètre d'un CRUD de règlements).
 */
class PaiementController extends Controller
{
    public function index(Eleve $eleve): View
    {
        $paiements = PaiementEleve::with(['cheques', 'options', 'etablissement'])
            ->where('id_eleve', $eleve->id_eleve)
            ->orderByDesc('date')
            ->get();

        return view('paiements.index', ['eleve' => $eleve, 'paiements' => $paiements]);
    }

    public function create(Eleve $eleve): View
    {
        $optionsDisponibles = NiveauxOptions::where('id_niveau', $eleve->id_niveau)
            ->where('annee', $eleve->annee_formation ?: date('Y'))
            ->orderBy('ordre')
            ->get();

        return view('paiements.create', ['eleve' => $eleve, 'optionsDisponibles' => $optionsDisponibles]);
    }

    /** Portage de `add_reglements()`. */
    public function store(Request $request, Eleve $eleve): RedirectResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:250'],
            'commentaire' => ['nullable', 'string'],
            'id_etablissement' => ['nullable', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'id_contrat' => ['nullable', 'integer'],
            'id_niveau' => ['nullable', 'integer'],
            'annee_rentree' => ['nullable', 'integer'],
            'mode_paiement' => ['nullable', 'in:CB,CHEQUE,VIREMENT'],
            'statut_paiement' => ['required', 'in:paye,accord_opco,cas_particulier'],
            'options' => ['nullable', 'array'],
            'options.*' => ['integer', 'exists:amos_niveaux_options,id_niveau_option'],
            'montant_option' => ['nullable', 'array'],
            'montant_option.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $paiement = DB::transaction(function () use ($data, $eleve) {
            $paiement = PaiementEleve::create([
                'id_eleve' => $eleve->id_eleve,
                'id_eleve_parent' => $eleve->id_eleve_parent ?: $eleve->id_eleve,
                'titre' => $data['titre'],
                'date' => now(),
                'date_modification' => now(),
                'commentaire' => $data['commentaire'] ?? '',
                'id_etablissement' => $data['id_etablissement'] ?? 0,
                'id_contrat' => $data['id_contrat'] ?? null,
                'id_niveau' => $data['id_niveau'] ?? null,
                'annee_rentree' => $data['annee_rentree'] ?? null,
                'accord_opco' => $data['statut_paiement'] === 'accord_opco',
                'mode_paiement' => $data['mode_paiement'] ?? null,
                'paiement_recu' => false,
                'type_saisie_apprentissage' => 1,
            ]);

            $libelles = [
                'paye' => 'Payé',
                'accord_opco' => 'Accord OPCO',
                'cas_particulier' => 'cas particulier',
            ];

            $eleve->update(['paiement_formation' => $libelles[$data['statut_paiement']]]);

            // Options facturées (catalogue du niveau) : le montant du catalogue est repris par
            // défaut, mais reste modifiable au cas par cas sur ce règlement précis — comme en legacy.
            foreach ($data['options'] ?? [] as $idNiveauOption) {
                $optionCatalogue = NiveauxOptions::find($idNiveauOption);
                if (! $optionCatalogue) {
                    continue;
                }

                PaiementEleveOption::create([
                    'id_paiement_eleve' => $paiement->id_paiement_eleve,
                    'id_eleve' => $eleve->id_eleve,
                    'id_niveau_option' => $idNiveauOption,
                    'montant' => $data['montant_option'][$idNiveauOption] ?? $optionCatalogue->montant,
                ]);
            }

            return $paiement;
        });

        return redirect()
            ->route('paiements.show', $paiement)
            ->with('status', 'Règlement créé avec succès.');
    }

    public function show(PaiementEleve $paiement): View
    {
        $paiement->load(['cheques', 'options', 'eleve.contact']);

        return view('paiements.show', ['paiement' => $paiement]);
    }

    /** Ajoute un chèque/versement à une saisie de règlement existante. */
    public function storeCheque(Request $request, PaiementEleve $paiement): RedirectResponse
    {
        $data = $request->validate([
            'numero_cheque' => ['nullable', 'string', 'max:50'],
            'montant_paiement' => ['required', 'numeric'],
            'nom_banque' => ['nullable', 'string', 'max:100'],
            'date_encaissement' => ['nullable', 'date'],
            'mode_paiement' => ['nullable', 'string', 'max:30'],
            'photo_cheque' => ['nullable', 'image', 'max:5120'],
        ]);

        $nomFichier = '';
        if ($request->hasFile('photo_cheque')) {
            $nomFichier = $request->file('photo_cheque')->hashName();
            $request->file('photo_cheque')->storeAs('cheques', $nomFichier, 'public');
        }

        ChequePaiement::create([
            'id_paiement_eleve' => $paiement->id_paiement_eleve,
            'id_objet_paiement' => 0,
            'id_etablissement' => $paiement->id_etablissement,
            'numero_cheque' => $data['numero_cheque'] ?? '',
            'montant_paiement' => $data['montant_paiement'],
            'nom_banque' => $data['nom_banque'] ?? '',
            'date_encaissement' => $data['date_encaissement'] ?? null,
            'mode_paiement' => $data['mode_paiement'] ?? null,
            'photo_cheque' => $nomFichier,
        ]);

        return redirect()
            ->route('paiements.show', $paiement)
            ->with('status', 'Versement ajouté.');
    }

    /** Portage de `delete_reglement()` (simplifié, voir en-tête de classe). */
    public function destroy(PaiementEleve $paiement): JsonResponse
    {
        $paiement->cheques()->delete();
        $paiement->options()->delete();
        $paiement->delete();

        return response()->json(['status' => 'success', 'message' => 'Suppression bien faite']);
    }
}
