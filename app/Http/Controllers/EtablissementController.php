<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Salle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Portage de la gestion des établissements (campus) dans `Referentiel.php`
 * (get_etablissement / add_etablissement / update_etablissement / valide_update_etablissement).
 *
 * L'établissement porte désormais son propre nom, saisi tel quel. Le legacy
 * le recomposait à chaque enregistrement (`SCHOOL_NAME . ' ' . ville`), ce
 * qui convient à un réseau dont tous les campus s'appellent « Marque VILLE »
 * mais pas à un groupe scolaire marocain. Surtout, l'écran de modification
 * en tirait la ville par `Str::after($nom, config('school.name'))`, qui
 * **rend la chaîne entière quand le préfixe est absent** : ouvrir puis
 * enregistrer « Groupe Scolaire Al Amal » le renommait en
 * « Scoleo GROUPE SCOLAIRE AL AMAL ».
 *
 * NON couvert : suppression d'établissement (absente aussi côté legacy).
 */
class EtablissementController extends Controller
{
    public function index(Request $request): View
    {
        $etablissements = Etablissement::query()
            ->when($request->filled('recherche'), fn ($q) => $q->where('nom_etablissement', 'like', '%'.$request->string('recherche').'%'))
            ->orderBy('nom_etablissement')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.etablissements.index', ['etablissements' => $etablissements]);
    }

    public function create(): View
    {
        return view('referentiel.etablissements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->valider($request);

        $etablissement = Etablissement::create([
            'nom_etablissement' => $data['nom_etablissement'],
            'code_ville' => mb_strtoupper($data['code_ville']),
            'adresse' => ucfirst($data['adresse']),
            'visible' => $request->boolean('visible'),
        ]);

        return redirect()
            ->route('referentiel.etablissements.edit', $etablissement)
            ->with('status', "Établissement « {$etablissement->nom_etablissement} » créé avec succès.");
    }

    public function edit(Etablissement $etablissement): View
    {
        return view('referentiel.etablissements.edit', [
            'etablissement' => $etablissement,
            'compteurs' => $this->compteurs($etablissement),
        ]);
    }

    /** Ce que l'établissement porte : on ne modifie pas un campus à l'aveugle. */
    private function compteurs(Etablissement $etablissement): array
    {
        return [
            'classes' => Classe::where('id_etablissement', $etablissement->id_etablissement)->count(),
            'eleves' => Eleve::whereIn(
                'id_classe',
                Classe::where('id_etablissement', $etablissement->id_etablissement)->pluck('id_classe')
            )->where('visible', true)->count(),
            'salles' => Salle::where('id_etablissement', $etablissement->id_etablissement)->count(),
        ];
    }

    private function valider(Request $request, ?Etablissement $etablissement = null): array
    {
        return $request->validate([
            'nom_etablissement' => [
                'required', 'string', 'max:100',
                Rule::unique('amos_etablissement', 'nom_etablissement')
                    ->ignore($etablissement?->id_etablissement, 'id_etablissement'),
            ],
            'code_ville' => ['required', 'string', 'max:2'],
            'adresse' => ['required', 'string', 'max:600'],
            'visible' => ['boolean'],
        ]);
    }

    public function update(Request $request, Etablissement $etablissement): RedirectResponse
    {
        $data = $this->valider($request, $etablissement);

        $etablissement->update([
            'nom_etablissement' => $data['nom_etablissement'],
            'code_ville' => mb_strtoupper($data['code_ville']),
            'adresse' => ucfirst($data['adresse']),
            'visible' => $request->boolean('visible'),
        ]);

        return redirect()
            ->route('referentiel.etablissements.edit', $etablissement)
            ->with('status', "Établissement « {$etablissement->nom_etablissement} » mis à jour.");
    }
}
