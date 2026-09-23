<?php

namespace App\Http\Controllers;

use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Etablissement;
use App\Models\Intervenant;
use App\Models\Salle;
use App\Support\Calendrier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage CRUD simple de `Planning.php` (CodeIgniter) — créneaux de cours
 * ponctuels, sans détection de conflit de disponibilité ni récurrence
 * (décision prise avec l'utilisateur : chantiers séparés, voir le modèle
 * `ActiviteIntervenant`).
 */
class PlanningController extends Controller
{
    /**
     * Emploi du temps de la semaine (vue par défaut) ou liste des créneaux.
     *
     * La liste restait la seule lecture possible : pour répondre à « la salle
     * 12 est-elle libre jeudi à 10 h ? », il fallait faire la grille de tête.
     * Le calendrier répond à vue d'œil, la liste reste disponible pour les
     * retouches en série.
     */
    public function index(Request $request): View
    {
        $filtres = [
            'intervenant' => $request->filled('intervenant') ? $request->integer('intervenant') : null,
            'campus' => $request->filled('campus') ? $request->integer('campus') : null,
            'classe' => $request->filled('classe') ? $request->integer('classe') : null,
        ];

        $vue = $request->string('vue')->toString() === 'liste' ? 'liste' : 'calendrier';
        $debutSemaine = $this->debutSemaine($request);

        $base = fn () => ActiviteIntervenant::with(['intervenant', 'etablissement', 'cours', 'classe', 'salle'])
            ->when($filtres['intervenant'], fn ($q, $v) => $q->where('id_intervenant', $v))
            ->when($filtres['campus'], fn ($q, $v) => $q->where('id_etablissement', $v))
            ->when($filtres['classe'], fn ($q, $v) => $q->where('id_classe', $v));

        $creneaux = $vue === 'liste'
            ? $base()->orderByDesc('date_debut')->paginate(25)->withQueryString()
            : null;

        $semaine = null;

        // Une grille horaire ne se lit que pour une classe ou un enseignant :
        // sans filtre, les 17 classes de l'école se superposent sur les mêmes
        // créneaux et plus rien n'est lisible. On demande donc de choisir.
        $cible = $filtres['classe'] || $filtres['intervenant'];

        if ($vue === 'calendrier' && $cible) {
            $duJour = $base()
                ->whereBetween('date_debut', [$debutSemaine, $debutSemaine->copy()->addDays(6)->endOfDay()])
                ->orderBy('date_debut')
                ->get();

            $semaine = Calendrier::semaine($duJour->map(fn (ActiviteIntervenant $c) => [
                'debut' => $c->date_debut,
                'fin' => $c->date_fin,
                'titre' => $c->cours?->nom_cours ?: 'Cours',
                'meta' => collect([
                    $c->classe?->classe,
                    $c->intervenant?->nom,
                    $c->salle?->nom_salle ? 'Salle '.$c->salle->nom_salle : null,
                ])->filter()->implode(' · '),
                // La couleur de la classe rend la grille lisible d'un coup d'œil.
                'couleur' => $c->classe?->couleur ?: '#4f46e5',
                // Le clic ouvre la fiche du créneau ; le lien reste là comme
                // repli si le script ne s'exécute pas.
                'url' => route('planning.edit', $c),
                'donnees' => [
                    'creneau' => $c->id_activite_intervenant,
                    'cours' => $c->id_cours,
                    'intervenant' => $c->id_intervenant,
                    'campus' => $c->id_etablissement,
                    'classe' => $c->id_classe,
                    'salle' => $c->id_salle,
                    'debut' => $c->date_debut->format('Y-m-d\TH:i'),
                    'fin' => $c->date_fin->format('Y-m-d\TH:i'),
                    'semestre' => $c->semestre,
                    'annee' => $c->annee,
                    'annotation' => $c->annotation,
                    'libelle' => $c->cours?->nom_cours ?: 'Cours',
                    'quand' => ucfirst($c->date_debut->translatedFormat('l j F')).' · '
                        .$c->date_debut->format('H:i').' – '.$c->date_fin->format('H:i'),
                    'classe-nom' => $c->classe?->classe ?: '—',
                    'intervenant-nom' => trim(($c->intervenant?->nom ?? '').' '.($c->intervenant?->prenom ?? '')) ?: '—',
                    'campus-nom' => $c->etablissement?->nom_etablissement ?: '—',
                    'salle-nom' => $c->salle?->nom_salle ?: '—',
                    'maj' => route('planning.update', $c),
                    'suppr' => route('planning.destroy', $c),
                ],
            ]), $debutSemaine);
        }

        return view('planning.index', [
            'vue' => $vue,
            'cible' => $cible,
            'filtres' => $filtres,
            'creneaux' => $creneaux,
            'semaine' => $semaine,
            'debutSemaine' => $debutSemaine,
            'intervenants' => Intervenant::orderBy('nom')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'classes' => Classe::orderBy('classe')->get(['id_classe', 'classe', 'couleur']),
            // Listes du formulaire de la fiche créneau (modification sur place).
            'cours' => Cours::orderBy('nom_cours')->get(['id_cours', 'nom_cours']),
            'salles' => Salle::orderBy('nom_salle')->get(['id_salle', 'nom_salle']),
        ]);
    }

    /** Lundi de la semaine demandée (`?semaine=AAAA-MM-JJ`), celui d'aujourd'hui par défaut. */
    private function debutSemaine(Request $request): Carbon
    {
        if ($request->filled('semaine')) {
            try {
                return Carbon::parse($request->string('semaine'))->startOfWeek();
            } catch (\Exception) {
                // Date illisible dans l'URL : on retombe sur la semaine courante
                // plutôt que de renvoyer une erreur.
            }
        }

        return Carbon::now()->startOfWeek();
    }

    public function create(): View
    {
        return view('planning.create', [
            'intervenants' => Intervenant::orderBy('nom')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'classes' => Classe::orderBy('classe')->get(),
            // `id_salle` est une colonne texte qui porte l'identifiant de la
            // salle : l'écran demandait de le taper à la main.
            'salles' => Salle::with('etablissement')->orderBy('nom_salle')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->completerChampsObligatoires($this->validerDonnees($request));

        $creneau = ActiviteIntervenant::create($data);

        return redirect()
            ->route('planning.edit', $creneau)
            ->with('status', 'Créneau créé avec succès.');
    }

    public function edit(ActiviteIntervenant $creneau): View
    {
        return view('planning.edit', [
            'creneau' => $creneau,
            'intervenants' => Intervenant::orderBy('nom')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'classes' => Classe::orderBy('classe')->get(),
            // `id_salle` est une colonne texte qui porte l'identifiant de la
            // salle : l'écran demandait de le taper à la main.
            'salles' => Salle::with('etablissement')->orderBy('nom_salle')->get(),
        ]);
    }

    public function update(Request $request, ActiviteIntervenant $creneau): RedirectResponse
    {
        $data = $this->completerChampsObligatoires($this->validerDonnees($request));

        $creneau->update($data);

        // `back()` plutôt qu'une route fixe : modifié depuis la fiche du
        // calendrier, on revient sur la semaine consultée et non sur le
        // formulaire plein écran.
        return redirect()
            ->back()
            ->with('status', 'Créneau mis à jour.');
    }

    public function destroy(ActiviteIntervenant $creneau): RedirectResponse
    {
        $creneau->delete();

        // Comme pour la mise à jour : on revient sur la semaine consultée
        // plutôt que sur l'écran de choix d'une classe.
        return redirect()
            ->back()
            ->with('status', 'Créneau supprimé.');
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'id_intervenant' => ['required', 'integer', 'exists:amos_intervenant,id_intervenant'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'id_cours' => ['required', 'integer', 'exists:amos_cours,id_cours'],
            'id_classe' => ['nullable', 'string', 'max:50'],
            'id_salle' => ['nullable', 'string', 'max:50'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
            'annotation' => ['nullable', 'string'],
            'semestre' => ['required', 'integer', 'in:1,2'],
            'annee' => ['required', 'integer'],
        ]);
    }

    /** Valeurs par défaut pour les colonnes NOT NULL sans défaut MySQL non couvertes par le formulaire. */
    private function completerChampsObligatoires(array $data): array
    {
        return [
            ...$data,
            'id_classe' => $data['id_classe'] ?? '',
            'id_salle' => $data['id_salle'] ?? '',
            'id_groupe' => '',
            'groupe' => '',
            'annotation' => $data['annotation'] ?? '',
        ];
    }
}
