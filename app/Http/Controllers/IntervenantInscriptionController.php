<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Etablissement;
use App\Models\Intervenant;
use App\Models\IntervenantCompetence;
use App\Models\IntervenantDiplome;
use App\Models\IntervenantSecteurActivite;
use App\Models\IntervenantSociete;
use App\Models\UserIntervenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage de `Intervenant_amos.php::add_intervenant_amos()` : formulaire
 * public d'auto-inscription d'un intervenant (accessible sans compte,
 * contrairement à `IntervenantController` qui est réservé aux admins).
 * Crée la fiche intervenant ET le compte de connexion espace-intervenant
 * (`amos_users_intervenant`), comble ainsi le point resté "non couvert"
 * dans la fiche Referentiel.php — Intervenants de `MIGRATION_PROGRESS.md`.
 *
 * **Non porté** : l'email de bienvenue (`get_email('new_intervenant')` +
 * `remplacer_mail()`) — le moteur de templates email n'est pas migré. À la
 * place, la page de confirmation affiche directement les identifiants de
 * connexion générés (l'utilisateur doit noter son mot de passe).
 */
class IntervenantInscriptionController extends Controller
{
    public function create(): View
    {
        return view('intervenant-inscription.create', [
            'cours' => Cours::orderBy('nom_cours')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'civilite' => ['required', 'string', 'max:10'],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:amos_users_intervenant,username'],
            'password' => ['required', 'string', 'min:8'],
            'date_naissance' => ['required', 'date'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:250'],
            'code_postal' => ['nullable', 'string', 'max:50'],
            'ville' => ['nullable', 'string', 'max:50'],
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
                'profession' => '',
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

            $user = new UserIntervenant([
                'id_intervenant' => $intervenant->id_intervenant,
                'username' => $data['email'],
                'connexion' => now(),
                'token' => '',
                'valide' => true,
            ]);
            $user->setPasswordFromPlain($data['password']);
            $user->save();

            return $intervenant;
        });

        Auth::guard('intervenant')->login(
            UserIntervenant::where('id_intervenant', $intervenant->id_intervenant)->firstOrFail()
        );

        return redirect()->route('espace-intervenant.planning')
            ->with('status', "Bienvenue {$intervenant->prenom}, votre compte intervenant a été créé.");
    }
}
