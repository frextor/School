<?php

namespace App\Http\Controllers;

use App\Models\AbsenceEleve;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Eleve;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Assiduité : appel par classe, suivi des absences et retards, justificatifs.
 *
 * Module absent du portage initial (l'assiduité était explicitement exclue,
 * cf. l'en-tête de `BulletinV2Controller`) et identifié comme le principal
 * manque fonctionnel lors du passage au K-12.
 *
 * S'appuie sur la table legacy `amos_absence_eleve` ; la traduction
 * nature/justification vers ses quatre booléens est centralisée dans
 * `AbsenceEleve::drapeaux()`.
 */
class AbsenceController extends Controller
{
    public function index(Request $request): View
    {
        $du = $request->date('du') ?: Carbon::now()->startOfMonth();
        $au = $request->date('au') ?: Carbon::now()->endOfMonth();

        $absences = AbsenceEleve::query()
            ->with(['eleve.contact', 'eleve.classe', 'cours'])
            ->whereBetween('date_absence', [$du->format('Y-m-d'), $au->format('Y-m-d')])
            ->when($request->filled('classe'), fn ($q) => $q->whereHas('eleve', fn ($q) => $q->where('id_classe', $request->integer('classe'))))
            ->when($request->filled('eleve'), fn ($q) => $q->where('id_eleve', $request->integer('eleve')))
            ->when($request->string('nature')->toString() === AbsenceEleve::NATURE_ABSENCE, fn ($q) => $q->absences())
            ->when($request->string('nature')->toString() === AbsenceEleve::NATURE_RETARD, fn ($q) => $q->retards())
            ->when($request->boolean('non_justifiees'), fn ($q) => $q->nonJustifiees())
            ->orderByDesc('date_absence')
            ->orderByDesc('heure_absence')
            ->paginate(40)
            ->withQueryString();

        // Compteurs calculés sur la même période/filtre de classe que la liste,
        // en base plutôt que sur la page courante (la pagination fausserait le total).
        $base = AbsenceEleve::query()
            ->whereBetween('date_absence', [$du->format('Y-m-d'), $au->format('Y-m-d')])
            ->when($request->filled('classe'), fn ($q) => $q->whereHas('eleve', fn ($q) => $q->where('id_classe', $request->integer('classe'))));

        return view('absences.index', [
            'absences' => $absences,
            'classes' => Classe::orderBy('classe')->get(),
            'filtres' => $request->only(['classe', 'eleve', 'nature', 'non_justifiees']),
            'du' => $du->format('Y-m-d'),
            'au' => $au->format('Y-m-d'),
            'compteurs' => [
                'total' => (clone $base)->count(),
                'absences' => (clone $base)->absences()->count(),
                'retards' => (clone $base)->retards()->count(),
                'non_justifiees' => (clone $base)->nonJustifiees()->count(),
            ],
        ]);
    }

    /**
     * Écran d'appel : la classe, la date et l'heure choisies déterminent la
     * séance ; les absences déjà saisies pour cette séance pré-cochent la liste,
     * ce qui permet de refaire un appel sans créer de doublon.
     */
    public function appel(Request $request): View
    {
        $classe = $request->filled('classe') ? Classe::find($request->integer('classe')) : null;
        $date = $request->date('date') ?: Carbon::today();
        $heure = $request->string('heure')->toString() ?: '08:00';

        $eleves = collect();
        $existantes = collect();

        if ($classe) {
            $eleves = Eleve::query()
                ->where('id_classe', $classe->id_classe)
                ->where('profil', Eleve::PROFIL_ELEVE)
                ->where('visible', true)
                ->with('contact')
                ->get()
                ->sortBy(fn (Eleve $e) => $e->contact?->nom_complet)
                ->values();

            $existantes = AbsenceEleve::where('date_absence', $date->format('Y-m-d'))
                ->where('heure_absence', $heure.':00')
                ->whereIn('id_eleve', $eleves->pluck('id_eleve'))
                ->get()
                ->keyBy('id_eleve');
        }

        return view('absences.appel', [
            'classes' => Classe::orderBy('classe')->get(),
            'cours' => Cours::orderBy('nom_cours')->get(),
            'classe' => $classe,
            'date' => $date->format('Y-m-d'),
            'heure' => $heure,
            'idCours' => $request->integer('id_cours'),
            'eleves' => $eleves,
            'existantes' => $existantes,
        ]);
    }

    /**
     * Enregistre un appel complet. Idempotent : les élèves repassés « présent »
     * voient leur ligne supprimée, les autres sont créés ou mis à jour — refaire
     * l'appel d'une séance corrige donc la saisie au lieu de la dupliquer.
     */
    public function enregistrerAppel(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'classe' => ['required', 'integer', 'exists:amos_classe,id_classe'],
            'date' => ['required', 'date'],
            'heure' => ['required', 'date_format:H:i'],
            'id_cours' => ['nullable', 'integer'],
            'statuts' => ['required', 'array'],
            'statuts.*' => ['in:present,absence,retard'],
        ]);

        $date = Carbon::parse($data['date']);
        $semestre = AbsenceEleve::semestrePour($date);
        $compte = ['absence' => 0, 'retard' => 0, 'efface' => 0];

        DB::transaction(function () use ($data, $date, $semestre, &$compte) {
            foreach ($data['statuts'] as $idEleve => $statut) {
                $existante = AbsenceEleve::where('id_eleve', (int) $idEleve)
                    ->where('date_absence', $date->format('Y-m-d'))
                    ->where('heure_absence', $data['heure'].':00')
                    ->first();

                if ($statut === 'present') {
                    if ($existante) {
                        $existante->delete();
                        $compte['efface']++;
                    }

                    continue;
                }

                // Une saisie existante conserve sa justification : l'appel ne
                // doit pas effacer un justificatif déjà fourni par la famille.
                $justifie = $existante?->justifie ?? false;

                AbsenceEleve::updateOrCreate(
                    [
                        'id_eleve' => (int) $idEleve,
                        'date_absence' => $date->format('Y-m-d'),
                        'heure_absence' => $data['heure'].':00',
                    ],
                    AbsenceEleve::drapeaux($statut, $justifie) + [
                        'id_cours' => $data['id_cours'] ?: 0,
                        'id_unite_enseignement' => 0,
                        'semestre' => $semestre,
                        'valide' => 1,
                        'annotation' => $existante->annotation ?? '',
                        'justificatif' => $justifie,
                        'modification_justificatif' => 0,
                    ]
                );

                $compte[$statut]++;
            }
        });

        return redirect()
            ->route('absences.appel', [
                'classe' => $data['classe'],
                'date' => $data['date'],
                'heure' => $data['heure'],
                'id_cours' => $data['id_cours'],
            ])
            ->with('status', "Appel enregistré : {$compte['absence']} absence(s), {$compte['retard']} retard(s)"
                .($compte['efface'] ? ", {$compte['efface']} ligne(s) retirée(s)" : '').'.');
    }

    /** Justifie une absence (motif + justificatif optionnel), ou retire la justification. */
    public function justifier(Request $request, AbsenceEleve $absence): RedirectResponse
    {
        $data = $request->validate([
            'justifie' => ['required', 'boolean'],
            'annotation' => ['nullable', 'string', 'max:250'],
            'piece' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $justifie = (bool) $data['justifie'];
        $fichier = $absence->justificatif_fichers;

        if ($request->hasFile('piece')) {
            $fichier = $request->file('piece')->hashName();
            $request->file('piece')->storeAs('justificatifs', $fichier, 'public');
        }

        $absence->update(AbsenceEleve::drapeaux($absence->nature, $justifie) + [
            'annotation' => $data['annotation'] ?? '',
            'justificatif' => $justifie,
            'justificatif_fichers' => $fichier,
            'date_justificatif' => $justifie ? now() : null,
            'modification_justificatif' => $absence->modification_justificatif + 1,
        ]);

        return back()->with('status', $justifie ? 'Absence justifiée.' : 'Justification retirée.');
    }

    public function destroy(AbsenceEleve $absence): RedirectResponse
    {
        $absence->delete();

        return back()->with('status', 'Ligne d\'assiduité supprimée.');
    }
}
