<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de la gestion des établissements (campus) dans `Referentiel.php`
 * (get_etablissement / add_etablissement / update_etablissement / valide_update_etablissement).
 *
 * Différence assumée : `SCHOOL_NAME . ' ' . strtoupper($ville)` (constante
 * legacy en dur) devient `config('school.name') . ' ' . strtoupper($ville)`
 * — voir config/school.php et SCHOOL_NAME dans .env.
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
        $data = $request->validate([
            'ville' => ['required', 'string', 'max:60'],
            'code_ville' => ['required', 'string', 'max:2'],
            'adresse' => ['required', 'string', 'max:600'],
            'visible' => ['boolean'],
        ]);

        $etablissement = Etablissement::create([
            'nom_etablissement' => config('school.name').' '.mb_strtoupper($data['ville']),
            'code_ville' => $data['code_ville'],
            'adresse' => ucfirst($data['adresse']),
            'visible' => $request->boolean('visible'),
        ]);

        return redirect()
            ->route('referentiel.etablissements.edit', $etablissement)
            ->with('status', "Établissement « {$etablissement->nom_etablissement} » créé avec succès.");
    }

    public function edit(Etablissement $etablissement): View
    {
        return view('referentiel.etablissements.edit', ['etablissement' => $etablissement]);
    }

    public function update(Request $request, Etablissement $etablissement): RedirectResponse
    {
        $data = $request->validate([
            'ville' => ['required', 'string', 'max:60'],
            'code_ville' => ['required', 'string', 'max:2'],
            'adresse' => ['required', 'string', 'max:600'],
            'visible' => ['boolean'],
        ]);

        $etablissement->update([
            'nom_etablissement' => config('school.name').' '.mb_strtoupper($data['ville']),
            'code_ville' => $data['code_ville'],
            'adresse' => ucfirst($data['adresse']),
            'visible' => $request->boolean('visible'),
        ]);

        return redirect()
            ->route('referentiel.etablissements.edit', $etablissement)
            ->with('status', "Établissement « {$etablissement->nom_etablissement} » mis à jour.");
    }
}
