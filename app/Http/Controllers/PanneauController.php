<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\GroupeEleve;
use App\Models\PanneauLumineux;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage de `Panneaux.php` (CodeIgniter) — configuration des panneaux
 * d'affichage dynamique (identifiant, établissement, formations/classes/
 * groupes concernés).
 *
 * L'affichage live (`show_panneau`, `get_cours_panneau`) est désormais porté
 * lui aussi : il attendait le module Planning, migré depuis. `plage_horaire`
 * reste le nombre d'heures affichées à partir de maintenant et `delai_horaire`
 * l'intervalle de rafraîchissement en secondes, comme en legacy.
 */
class PanneauController extends Controller
{
    public function index(): View
    {
        $panneaux = PanneauLumineux::with(['etablissement', 'classes', 'groupes'])
            ->orderBy('titre')
            ->paginate(25);

        return view('panneaux.index', ['panneaux' => $panneaux]);
    }

    /**
     * Écran affiché par le panneau lui-même, sans authentification : un
     * téléviseur de couloir ne peut pas se connecter. L'adresse est celle
     * que l'on saisit une fois dans le navigateur de l'écran.
     *
     * L'affichage live était annoncé comme non portable tant que le module
     * Planning ne l'était pas ; il l'est depuis, d'où cet écran.
     */
    public function affichage(string $identifiant): View
    {
        $panneau = PanneauLumineux::with(['etablissement', 'classes', 'groupes'])
            ->where('identifiant_panneaux', $identifiant)
            ->firstOrFail();

        $debut = Carbon::now();
        $fin = $debut->copy()->addHours(max(1, (int) $panneau->plage_horaire));

        $idsClasses = $panneau->classes->pluck('id_classe');
        $nomsGroupes = $panneau->groupes->pluck('nom_groupe')->filter();

        $seances = ActiviteIntervenant::with(['cours', 'classe', 'intervenant', 'salle'])
            ->where('id_etablissement', $panneau->id_etablissement)
            ->whereBetween('date_debut', [$debut, $fin])
            // Un panneau sans classe ni groupe affiche tout l'établissement :
            // c'est le cas d'un écran d'accueil.
            ->when($idsClasses->isNotEmpty() || $nomsGroupes->isNotEmpty(), function ($q) use ($idsClasses, $nomsGroupes) {
                $q->where(function ($q) use ($idsClasses, $nomsGroupes) {
                    $q->when($idsClasses->isNotEmpty(), fn ($q) => $q->whereIn('id_classe', $idsClasses))
                        ->when($nomsGroupes->isNotEmpty(), fn ($q) => $q->orWhereIn('groupe', $nomsGroupes));
                });
            })
            ->orderBy('date_debut')
            ->limit(30)
            ->get();

        return view('panneaux.affichage', [
            'panneau' => $panneau,
            'seances' => $seances,
            'fin' => $fin,
        ]);
    }

    public function create(): View
    {
        return view('panneaux.create', [
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'formations' => Formation::orderBy('niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(),
            'groupes' => GroupeEleve::orderBy('nom_groupe')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request);

        $panneau = DB::transaction(fn () => $this->enregistrer(new PanneauLumineux(), $data));

        return redirect()
            ->route('panneaux.edit', $panneau)
            ->with('status', "Panneau « {$panneau->titre} » créé avec succès.");
    }

    public function edit(PanneauLumineux $panneau): View
    {
        $panneau->load(['formations', 'classes', 'groupes']);

        return view('panneaux.edit', [
            'panneau' => $panneau,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'formations' => Formation::orderBy('niveau')->get(),
            'classes' => Classe::orderBy('classe')->get(),
            'groupes' => GroupeEleve::orderBy('nom_groupe')->get(),
        ]);
    }

    public function update(Request $request, PanneauLumineux $panneau): RedirectResponse
    {
        $data = $this->validerDonnees($request, $panneau);

        DB::transaction(fn () => $this->enregistrer($panneau, $data));

        return redirect()
            ->route('panneaux.edit', $panneau)
            ->with('status', "Panneau « {$panneau->titre} » mis à jour.");
    }

    public function destroy(PanneauLumineux $panneau): RedirectResponse
    {
        DB::transaction(function () use ($panneau) {
            $panneau->formations()->detach();
            $panneau->classes()->detach();
            $panneau->groupes()->detach();
            $panneau->delete();
        });

        return redirect()
            ->route('panneaux.index')
            ->with('status', 'Panneau supprimé.');
    }

    /** Portage de `check_identifiant_panneaux()`. */
    public function checkIdentifiant(Request $request): JsonResponse
    {
        $identifiant = $request->string('identifiant_panneaux');
        $idPanneau = $request->integer('id_panneau') ?: null;

        $panneau = PanneauLumineux::where('identifiant_panneaux', $identifiant)
            ->when($idPanneau, fn ($q) => $q->where('id_panneau', '!=', $idPanneau))
            ->with('etablissement')
            ->first();

        if ($panneau) {
            return response()->json([
                'status' => 'success',
                'message' => "Cet ID panneau est déjà utilisé sur {$panneau->etablissement?->nom_etablissement}. Veuillez choisir un autre.",
            ]);
        }

        return response()->json(['status' => 'empty', 'message' => 'Valide']);
    }

    private function validerDonnees(Request $request, ?PanneauLumineux $panneau = null): array
    {
        $idPanneau = $panneau?->id_panneau;

        return $request->validate([
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'identifiant_panneaux' => ['required', 'string', 'max:255', 'unique:amos_panneaux_lumineux,identifiant_panneaux,'.$idPanneau.',id_panneau'],
            'titre' => ['required', 'string', 'max:255'],
            'annee' => ['nullable', 'string', 'max:250'],
            'plage_horaire' => ['required', 'integer', 'min:1'],
            'delai_horaire' => ['required', 'integer', 'min:0'],
            'formations' => ['array'],
            'formations.*' => ['integer', 'exists:amos_formations,id_formation'],
            'classes' => ['array'],
            'classes.*' => ['integer', 'exists:amos_classe,id_classe'],
            'groupes' => ['array'],
            'groupes.*' => ['integer', 'exists:amos_eleves_groupes,id_groupe'],
        ]);
    }

    private function enregistrer(PanneauLumineux $panneau, array $data): PanneauLumineux
    {
        $panneau->fill([
            'id_etablissement' => $data['id_etablissement'],
            'identifiant_panneaux' => $data['identifiant_panneaux'],
            'titre' => $data['titre'],
            'annee' => $data['annee'] ?? '',
            'plage_horaire' => $data['plage_horaire'],
            'delai_horaire' => $data['delai_horaire'],
        ])->save();

        $panneau->formations()->sync($data['formations'] ?? []);
        $panneau->classes()->sync($data['classes'] ?? []);
        $panneau->groupes()->sync($data['groupes'] ?? []);

        return $panneau;
    }
}
