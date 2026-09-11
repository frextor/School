<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\EntrepriseSecteurActivite;
use App\Models\Etablissement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage du cœur de `Entreprises.php` + `Entreprises_library::save_entreprise()`
 * (add_entreprise / valide_update_entreprise / delete_entreprise / liste).
 *
 * Simplifications et exclusions assumées :
 * - **Aucun appel Mautic** (`mautic_api_model->add_entreprise_contact`,
 *   `send_email_with_params`) — exclu à la demande de l'utilisateur.
 * - Le workflow de tâches/relances (`add_tache`, `handle_tache_workflow`,
 *   rappels/RDV) et la journalisation (`set_trace_entreprise`) ne sont pas
 *   portés : ce sont des sous-systèmes CRM à part entière, à traiter
 *   séparément (relances commerciales).
 * - Les nombreux "référentiels" annexes de ce controller (types de contrat,
 *   gestion TVA, types de poste, recherches, départements, familles de
 *   sources...) via les `modal_*` ne sont pas dupliqués ici : ce sont de
 *   simples CRUD "libellé" du même moule que ceux déjà portés dans
 *   `Referentiel.php` — à ajouter au fil de l'eau si besoin.
 */
class EntrepriseController extends Controller
{
    public function index(Request $request): View
    {
        $entreprises = Entreprise::query()
            ->with(['etablissement', 'secteur'])
            ->when($request->filled('recherche'), fn ($q) => $q->where('nom_entreprise', 'like', '%'.$request->string('recherche').'%'))
            ->orderBy('nom_entreprise')
            ->paginate(25)
            ->withQueryString();

        return view('entreprises.index', ['entreprises' => $entreprises, 'filtres' => $request->only('recherche')]);
    }

    public function create(): View
    {
        return view('entreprises.create', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'secteurs' => EntrepriseSecteurActivite::orderBy('nom_secteur')->get(),
            'entreprisesMeres' => Entreprise::whereNull('id_parent')->orderBy('nom_entreprise')->get(),
        ]);
    }

    /** Portage de `Entreprises_library::add_entreprise()` (hors intégration Mautic). */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request);

        if (Entreprise::where('email', $data['email'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'Cette adresse email existe déjà.']);
        }

        $data['information_complementaire'] ??= '';

        $entreprise = Entreprise::create($data);

        return redirect()
            ->route('entreprises.show', $entreprise)
            ->with('status', "Entreprise « {$entreprise->nom_entreprise} » créée avec succès.");
    }

    public function show(Entreprise $entreprise): View
    {
        $entreprise->load(['contacts', 'etablissement', 'secteur', 'filiales', 'compteLoginPortail']);

        return view('entreprises.show', ['entreprise' => $entreprise]);
    }

    public function edit(Entreprise $entreprise): View
    {
        return view('entreprises.edit', [
            'entreprise' => $entreprise,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'secteurs' => EntrepriseSecteurActivite::orderBy('nom_secteur')->get(),
            'entreprisesMeres' => Entreprise::whereNull('id_parent')->where('id_entreprise', '!=', $entreprise->id_entreprise)->orderBy('nom_entreprise')->get(),
        ]);
    }

    /** Portage de `Entreprises_library::valide_update_entreprise()` (hors workflow tâches/relances). */
    public function update(Request $request, Entreprise $entreprise): RedirectResponse
    {
        $data = $this->validerDonnees($request, $entreprise);

        $entreprise->update($data);

        return redirect()
            ->route('entreprises.show', $entreprise)
            ->with('status', "Entreprise « {$entreprise->nom_entreprise} » mise à jour.");
    }

    public function destroy(Entreprise $entreprise): JsonResponse
    {
        $entreprise->contacts()->delete();
        $entreprise->compteLoginPortail()?->delete();
        $entreprise->delete();

        return response()->json('ok');
    }

    private function validerDonnees(Request $request, ?Entreprise $entreprise = null): array
    {
        $idEntreprise = $entreprise?->id_entreprise;

        return $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:250'],
            'type_entreprise' => ['nullable', 'in:Entreprise mère,Entreprise,OPCO'],
            'email' => ['required', 'email', 'max:250', 'unique:amos_entreprises,email,'.$idEntreprise.',id_entreprise'],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse' => ['nullable', 'string'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'ville' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:50'],
            'site_web' => ['nullable', 'url', 'max:500'],
            'siret' => ['nullable', 'integer'],
            'numero_tva' => ['nullable', 'string', 'max:20'],
            'id_etablissement' => ['nullable', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'id_secteur' => ['nullable', 'integer', 'exists:amos_entreprises_secteurs_activites,id_entreprises_secteurs_activites'],
            'id_parent' => ['nullable', 'integer', 'exists:amos_entreprises,id_entreprise'],
            'information_complementaire' => ['nullable', 'string'],
        ]);
    }
}
