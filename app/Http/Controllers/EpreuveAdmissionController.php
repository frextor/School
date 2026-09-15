<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\EpreuveAdmission;
use App\Models\EpreuveAdmissionEleve;
use App\Models\Formation;
use App\Models\MotifsRefusCandidat;
use App\Models\ResultatEpreuveEleve;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage du sous-système "épreuves d'admission" de `Candidats.php`
 * (add_epreuve / epreuves / update_epreuve / supprimer_epreuves /
 * update_epreuve_presence / add_resultats / delete_resultat / archive_resultat).
 */
class EpreuveAdmissionController extends Controller
{
    public function index(): View
    {
        $epreuves = EpreuveAdmission::with('formations')
            ->withCount(['inscriptions', 'resultats'])
            ->orderByDesc('date_epreuve')
            ->paginate(25);

        return view('epreuves.index', ['epreuves' => $epreuves]);
    }

    public function create(): View
    {
        return view('epreuves.create', ['formations' => Formation::orderBy('niveau')->get()]);
    }

    /** Portage de `add_epreuve()`. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'heure' => ['required'],
            'lieu' => ['required', 'string', 'max:16'],
            'effectif' => ['required', 'integer', 'min:1'],
            'distanciel' => ['boolean'],
            'url_distanciel' => ['nullable', 'url'],
            'id_formation' => ['array'],
            'id_formation.*' => ['integer', 'exists:amos_formations,id_formation'],
        ]);

        $existe = EpreuveAdmission::where('date_epreuve', "{$data['date']} {$data['heure']}")
            ->where('lieu', $data['lieu'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['lieu' => "Vous avez déjà une épreuve d'admission correspondant à ces informations."]);
        }

        $epreuve = DB::transaction(function () use ($data, $request) {
            $epreuve = EpreuveAdmission::create([
                'date_epreuve' => "{$data['date']} {$data['heure']}",
                'lieu' => $data['lieu'],
                'effectif' => $data['effectif'],
                'distanciel' => $request->boolean('distanciel'),
                'url_distanciel' => $data['url_distanciel'] ?? '',
            ]);

            $epreuve->formations()->sync($data['id_formation'] ?? []);

            return $epreuve;
        });

        return redirect()
            ->route('epreuves.index')
            ->with('status', "Épreuve d'admission créée avec succès.");
    }

    public function edit(EpreuveAdmission $epreuve): View
    {
        $epreuve->load([
            'formations',
            'inscriptions.eleve.contact',
            'resultats',
        ]);

        $idsInscrits = $epreuve->inscriptions->pluck('id_eleve');

        return view('epreuves.edit', [
            'epreuve' => $epreuve,
            'formations' => Formation::orderBy('niveau')->get(),
            // Résultat existant par candidat, pour pré-remplir le formulaire de notes.
            'resultatsParEleve' => $epreuve->resultats->keyBy('id_eleve'),
            // Candidats déjà inscrits exclus, pour ne pas doubler une inscription.
            'candidatsDisponibles' => Eleve::candidats()
                ->whereNotIn('id_eleve', $idsInscrits)
                ->with('contact')
                ->get()
                ->filter(fn (Eleve $c) => $c->contact)
                ->sortBy(fn (Eleve $c) => $c->contact->nom_complet),
            'motifsRefus' => MotifsRefusCandidat::orderBy('libelle')->get(),
        ]);
    }

    /** Portage de `update_epreuve()`. */
    public function update(Request $request, EpreuveAdmission $epreuve): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'heure' => ['required'],
            'lieu' => ['required', 'string', 'max:16'],
            'effectif' => ['required', 'integer', 'min:1'],
            'distanciel' => ['boolean'],
            'url_distanciel' => ['nullable', 'url'],
            'id_formation' => ['array'],
            'id_formation.*' => ['integer', 'exists:amos_formations,id_formation'],
        ]);

        $epreuve->update([
            'date_epreuve' => "{$data['date']} {$data['heure']}",
            'lieu' => $data['lieu'],
            'effectif' => $data['effectif'],
            'distanciel' => $request->boolean('distanciel'),
            'url_distanciel' => $data['url_distanciel'] ?? '',
        ]);

        $epreuve->formations()->sync($data['id_formation'] ?? []);

        return redirect()
            ->route('epreuves.index')
            ->with('status', "Épreuve d'admission mise à jour.");
    }

    /** Portage de `supprimer_epreuves()`. */
    public function destroy(EpreuveAdmission $epreuve): RedirectResponse
    {
        DB::transaction(function () use ($epreuve) {
            $epreuve->formations()->detach();
            $epreuve->inscriptions()->delete();
            $epreuve->delete();
        });

        return redirect()
            ->route('epreuves.index')
            ->with('status', "Épreuve d'admission supprimée.");
    }

    /** Portage de `set_candidats_to_epreuve()` — inscrit un candidat à une épreuve. */
    public function inscrireCandidat(Request $request, EpreuveAdmission $epreuve): RedirectResponse
    {
        $data = $request->validate(['id_eleve' => ['required', 'integer', 'exists:amos_eleves,id_eleve']]);

        EpreuveAdmissionEleve::firstOrCreate([
            'id_epreuve_admission' => $epreuve->id_epreuve_admission,
            'id_eleve' => $data['id_eleve'],
        ], ['presence' => false]);

        return back()->with('status', 'Candidat inscrit à cette épreuve.');
    }

    /** Portage de `update_epreuve_presence()`. */
    public function togglePresence(EpreuveAdmissionEleve $inscription): RedirectResponse
    {
        $inscription->update(['presence' => ! $inscription->presence]);

        return back()->with('status', 'Présence mise à jour.');
    }

    public function suppressionCandidat(EpreuveAdmission $epreuve, Eleve $candidat): RedirectResponse
    {
        EpreuveAdmissionEleve::where('id_epreuve_admission', $epreuve->id_epreuve_admission)
            ->where('id_eleve', $candidat->id_eleve)
            ->delete();

        return back()->with('status', 'Candidat retiré de cette épreuve.');
    }

    /** Portage de `add_resultats()` — saisie/mise à jour des notes et de la décision. */
    public function storeResultat(Request $request, EpreuveAdmission $epreuve): RedirectResponse
    {
        $data = $request->validate([
            'id_eleve' => ['required', 'integer', 'exists:amos_eleves,id_eleve'],
            'anglais' => ['required', 'integer', 'min:0'],
            'culture_generale' => ['required', 'integer', 'min:0'],
            'epreuve_redaction' => ['required', 'integer', 'min:0'],
            'entretien' => ['required', 'integer', 'min:0'],
            'decision' => ['required', 'in:accepte,accepter_niveau_inferieur,refuse,en_attente,accepter_avec_entreprise'],
            'id_motif_refus' => ['nullable', 'integer'],
        ]);

        ResultatEpreuveEleve::updateOrCreate(
            ['id_eleve' => $data['id_eleve'], 'id_epreuve_admission' => $epreuve->id_epreuve_admission],
            [
                'anglais' => $data['anglais'],
                'culture_generale' => $data['culture_generale'],
                'epreuve_redaction' => $data['epreuve_redaction'],
                'entretien' => $data['entretien'],
                'decision' => $data['decision'],
                'id_motif_refus' => $data['id_motif_refus'] ?? null,
                'date_operation' => now(),
            ]
        );

        return back()->with('status', 'Résultat enregistré.');
    }

    /** Portage de `delete_resultat()`. */
    public function destroyResultat(ResultatEpreuveEleve $resultat): RedirectResponse
    {
        $resultat->delete();

        return back()->with('status', 'Résultat supprimé.');
    }

    /** Portage de `archive_resultat()`. */
    public function archiveResultat(ResultatEpreuveEleve $resultat): RedirectResponse
    {
        $resultat->update(['archive' => ! $resultat->archive]);

        return back()->with('status', $resultat->archive ? 'Résultat archivé.' : 'Résultat désarchivé.');
    }
}
