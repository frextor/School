<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Salle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Portage de la gestion des salles dans `Parametrage_planning.php`. */
class SalleController extends Controller
{
    public function index(): View
    {
        return view('salles.index', ['salles' => Salle::with('etablissement')->orderBy('nom_salle')->paginate(25)]);
    }

    public function create(): View
    {
        return view('salles.create', ['etablissements' => Etablissement::orderBy('nom_etablissement')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request);

        Salle::create([...$data, 'date_creation' => now(), 'ip' => $request->ip()]);

        return redirect()->route('salles.index')->with('status', 'Salle créée avec succès.');
    }

    public function edit(Salle $salle): View
    {
        return view('salles.edit', ['salle' => $salle, 'etablissements' => Etablissement::orderBy('nom_etablissement')->get()]);
    }

    public function update(Request $request, Salle $salle): RedirectResponse
    {
        $salle->update($this->validerDonnees($request));

        return redirect()->route('salles.index')->with('status', 'Salle mise à jour.');
    }

    public function destroy(Salle $salle): RedirectResponse
    {
        $salle->delete();

        return redirect()->route('salles.index')->with('status', 'Salle supprimée.');
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'code_salle' => ['required', 'string', 'max:5'],
            'nom_salle' => ['required', 'string', 'max:50'],
            'nombre_place' => ['required', 'integer', 'min:0'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
        ]);
    }
}
