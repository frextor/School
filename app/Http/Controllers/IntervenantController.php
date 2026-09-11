<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Intervenant;
use App\Models\IntervenantCompetence;
use App\Models\IntervenantDiplome;
use App\Models\IntervenantSecteurActivite;
use App\Models\Cours;
use App\Models\IntervenantSociete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Portage de la gestion des intervenants dans `Referentiel.php`
 * (get_intervenant / intervenant_form_add / add_intervenant / supp_intervenant).
 *
 * NON couvert (à traiter séparément, hors périmètre d'un CRUD de fiche) :
 * - Création du compte de connexion intervenant (`amos_users_intervenant`)
 *   et de l'email de bienvenue — dépend du guard d'auth "intervenant" et du
 *   moteur de templates email (`get_email` / `remplacer_mail`), non migrés.
 */
class IntervenantController extends Controller
{
    public function index(Request $request): View
    {
        $intervenants = Intervenant::query()
            ->with('societe')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom', 'like', "%{$terme}%")->orWhere('prenom', 'like', "%{$terme}%");
            })
            ->orderBy('nom')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.intervenants.index', ['intervenants' => $intervenants]);
    }

    public function create(): View
    {
        return view('referentiel.intervenants.create', [
            'cours' => Cours::orderBy('nom_cours')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request);
        $data['date_naissance'] = $request->validate(['date_naissance' => ['required', 'date']])['date_naissance'];

        $intervenant = DB::transaction(function () use ($data, $request) {
            $societe = null;
            if (! empty($data['raison_sociale'])) {
                $societe = IntervenantSociete::create([
                    'raison_sociale' => $data['raison_sociale'],
                    'adresse_societe' => $data['adresse_societe'] ?? '',
                    'tel_societe' => '',
                    'fax_societe' => '',
                    'email_societe' => '',
                    'id_secteur_activite' => 0,
                ]);
            }

            // La colonne `cv`/`photo` (varchar(50), héritée du legacy) ne stocke que le
            // nom de fichier généré ; le fichier physique vit dans un dossier fixe
            // (Intervenant::dossierCv() / dossierPhotos()) reconstruit à la lecture.
            $nomFichierCv = '';
            if ($request->hasFile('cv')) {
                $nomFichierCv = $request->file('cv')->hashName();
                $request->file('cv')->storeAs('intervenants/cv', $nomFichierCv, 'public');
            }

            $nomFichierPhoto = '';
            if ($request->hasFile('photo')) {
                $nomFichierPhoto = $request->file('photo')->hashName();
                $request->file('photo')->storeAs('intervenants/photos', $nomFichierPhoto, 'public');
            }

            $intervenant = Intervenant::create([
                'civilite' => $data['civilite'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'email_office' => '',
                'date_naissance' => $data['date_naissance'],
                'id_nationalite' => 0,
                'id_langue' => '',
                'telephone' => $data['telephone'] ?? '',
                'mobile' => $data['mobile'] ?? '',
                'adresse' => $data['adresse'] ?? '',
                'code_postal' => $data['code_postal'] ?? '',
                'ville' => $data['ville'] ?? '',
                'id_pays' => 0,
                'formation_suivie_intitule' => '',
                'niveau_formation_suivie' => '',
                'lieu_formation_suivie' => '',
                'profession' => $data['profession'] ?? '',
                'poste_actuel' => $data['poste_actuel'] ?? '',
                'cv' => $nomFichierCv,
                'photo' => $nomFichierPhoto,
                'id_societe' => $societe?->id_societe ?? 0,
            ]);

            foreach ($data['competences'] ?? [] as $competence) {
                if ($competence !== '') {
                    IntervenantCompetence::create(['nom_competence' => $competence, 'id_intervenant' => $intervenant->id_intervenant]);
                }
            }

            foreach ($data['diplomes'] ?? [] as $diplome) {
                if ($diplome !== '') {
                    IntervenantDiplome::create(['titre_diplome' => $diplome, 'id_intervenant' => $intervenant->id_intervenant]);
                }
            }

            foreach ($data['secteurs'] ?? [] as $secteur) {
                if ($secteur !== '') {
                    IntervenantSecteurActivite::create(['nom_secteur_activite' => $secteur, 'id_intervenant' => $intervenant->id_intervenant]);
                }
            }

            $intervenant->cours()->sync($data['cours'] ?? []);
            $intervenant->etablissements()->sync($data['etablissements'] ?? []);

            return $intervenant;
        });

        return redirect()
            ->route('referentiel.intervenants.show', $intervenant)
            ->with('status', "Intervenant « {$intervenant->nom} {$intervenant->prenom} » créé avec succès.");
    }

    public function show(Intervenant $intervenant): View
    {
        $intervenant->load(['societe', 'competences', 'diplomes', 'secteursActivite', 'cours', 'etablissements']);

        return view('referentiel.intervenants.show', ['intervenant' => $intervenant]);
    }

    public function edit(Intervenant $intervenant): View
    {
        $intervenant->load(['societe', 'competences', 'diplomes', 'secteursActivite', 'cours', 'etablissements']);

        return view('referentiel.intervenants.edit', [
            'intervenant' => $intervenant,
            'cours' => Cours::orderBy('nom_cours')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    /** Portage de `Intervenant.php::update_intervenant()` (hors mise à jour du compte de connexion — voir note de classe). */
    public function update(Request $request, Intervenant $intervenant): RedirectResponse
    {
        $data = $this->validerDonnees($request);

        DB::transaction(function () use ($data, $request, $intervenant) {
            if (! empty($data['raison_sociale'])) {
                if ($intervenant->id_societe) {
                    $intervenant->societe()->update([
                        'raison_sociale' => $data['raison_sociale'],
                        'adresse_societe' => $data['adresse_societe'] ?? '',
                    ]);
                } else {
                    $societe = IntervenantSociete::create([
                        'raison_sociale' => $data['raison_sociale'],
                        'adresse_societe' => $data['adresse_societe'] ?? '',
                        'tel_societe' => '',
                        'fax_societe' => '',
                        'email_societe' => '',
                        'id_secteur_activite' => 0,
                    ]);
                    $intervenant->id_societe = $societe->id_societe;
                }
            }

            if ($request->hasFile('cv')) {
                if ($chemin = $intervenant->cheminCv()) {
                    Storage::disk('public')->delete($chemin);
                }
                $intervenant->cv = $request->file('cv')->hashName();
                $request->file('cv')->storeAs('intervenants/cv', $intervenant->cv, 'public');
            }

            if ($request->hasFile('photo')) {
                if ($chemin = $intervenant->cheminPhoto()) {
                    Storage::disk('public')->delete($chemin);
                }
                $intervenant->photo = $request->file('photo')->hashName();
                $request->file('photo')->storeAs('intervenants/photos', $intervenant->photo, 'public');
            }

            $intervenant->fill([
                'civilite' => $data['civilite'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'] ?? '',
                'mobile' => $data['mobile'] ?? '',
                'adresse' => $data['adresse'] ?? '',
                'code_postal' => $data['code_postal'] ?? '',
                'ville' => $data['ville'] ?? '',
                'profession' => $data['profession'] ?? '',
                'poste_actuel' => $data['poste_actuel'] ?? '',
            ])->save();

            // Legacy : les compétences/diplômes/secteurs sont remplacés en totalité à chaque
            // modification — mais seulement si le champ est réellement soumis par le
            // formulaire (le formulaire d'édition actuel ne les expose pas encore, voir
            // referentiel/intervenants/edit.blade.php) pour ne pas effacer les données
            // existantes silencieusement.
            if ($request->has('competences')) {
                $intervenant->competences()->delete();
                foreach ($data['competences'] ?? [] as $competence) {
                    if ($competence !== '') {
                        IntervenantCompetence::create(['nom_competence' => $competence, 'id_intervenant' => $intervenant->id_intervenant]);
                    }
                }
            }

            if ($request->has('diplomes')) {
                $intervenant->diplomes()->delete();
                foreach ($data['diplomes'] ?? [] as $diplome) {
                    if ($diplome !== '') {
                        IntervenantDiplome::create(['titre_diplome' => $diplome, 'id_intervenant' => $intervenant->id_intervenant]);
                    }
                }
            }

            if ($request->has('secteurs')) {
                $intervenant->secteursActivite()->delete();
                foreach ($data['secteurs'] ?? [] as $secteur) {
                    if ($secteur !== '') {
                        IntervenantSecteurActivite::create(['nom_secteur_activite' => $secteur, 'id_intervenant' => $intervenant->id_intervenant]);
                    }
                }
            }

            $intervenant->cours()->sync($data['cours'] ?? []);
            $intervenant->etablissements()->sync($data['etablissements'] ?? []);
        });

        return redirect()
            ->route('referentiel.intervenants.show', $intervenant)
            ->with('status', "Intervenant « {$intervenant->nom} {$intervenant->prenom} » mis à jour.");
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'civilite' => ['required', 'string', 'max:10'],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:250'],
            'code_postal' => ['nullable', 'string', 'max:50'],
            'ville' => ['nullable', 'string', 'max:50'],
            'profession' => ['nullable', 'string', 'max:100'],
            'poste_actuel' => ['nullable', 'string', 'max:100'],
            'raison_sociale' => ['nullable', 'string', 'max:500'],
            'adresse_societe' => ['nullable', 'string', 'max:200'],
            'competences' => ['array'],
            'competences.*' => ['string', 'max:200'],
            'diplomes' => ['array'],
            'diplomes.*' => ['string', 'max:100'],
            'secteurs' => ['array'],
            'secteurs.*' => ['string', 'max:200'],
            'cours' => ['array'],
            'cours.*' => ['integer', 'exists:amos_cours,id_cours'],
            'etablissements' => ['array'],
            'etablissements.*' => ['integer', 'exists:amos_etablissement,id_etablissement'],
            'cv' => ['nullable', 'file', 'mimes:doc,docx,pdf', 'max:10240'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    public function destroy(Intervenant $intervenant): RedirectResponse
    {
        DB::transaction(function () use ($intervenant) {
            if ($chemin = $intervenant->cheminCv()) {
                Storage::disk('public')->delete($chemin);
            }
            if ($chemin = $intervenant->cheminPhoto()) {
                Storage::disk('public')->delete($chemin);
            }

            $intervenant->competences()->delete();
            $intervenant->diplomes()->delete();
            $intervenant->secteursActivite()->delete();
            $intervenant->cours()->detach();
            $intervenant->etablissements()->detach();
            $intervenant->delete();
        });

        return redirect()
            ->route('referentiel.intervenants.index')
            ->with('status', 'Intervenant supprimé.');
    }
}
