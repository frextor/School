<?php

namespace App\Http\Controllers;

use App\Models\Specialisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de la gestion des spécialisations dans `Referentiel.php`
 * (add_specialisation / supp_specialisation / update_specialisation / valide_update_specialisation).
 */
class SpecialisationController extends Controller
{
    public function index(Request $request): View
    {
        $specialisations = Specialisation::query()
            ->when($request->filled('recherche'), fn ($q) => $q->where('nom_specialisation', 'like', '%'.$request->string('recherche').'%'))
            ->orderBy('nom_specialisation')
            ->paginate(25)
            ->withQueryString();

        return view('referentiel.specialisations.index', ['specialisations' => $specialisations]);
    }

    public function create(): View
    {
        return view('referentiel.specialisations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nom_specialisation' => ['required', 'string', 'max:150']]);

        $specialisation = Specialisation::create([
            'nom_specialisation' => $data['nom_specialisation'],
            'date_creation' => now(),
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('referentiel.specialisations.index')
            ->with('status', "Spécialisation « {$specialisation->nom_specialisation} » créée avec succès.");
    }

    public function edit(Specialisation $specialisation): View
    {
        return view('referentiel.specialisations.edit', ['specialisation' => $specialisation]);
    }

    public function update(Request $request, Specialisation $specialisation): RedirectResponse
    {
        $data = $request->validate(['nom_specialisation' => ['required', 'string', 'max:150']]);

        $specialisation->update([
            'nom_specialisation' => $data['nom_specialisation'],
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('referentiel.specialisations.index')
            ->with('status', "Spécialisation « {$specialisation->nom_specialisation} » mise à jour.");
    }

    public function destroy(Specialisation $specialisation): RedirectResponse
    {
        $specialisation->delete();

        return redirect()
            ->route('referentiel.specialisations.index')
            ->with('status', 'Spécialisation supprimée.');
    }
}
