<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\ReferentielVacance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage unifié de `Parametrage_planning.php` : les 6 variantes legacy
 * (`add_ferie`/`add_vacance`/`add_fermeture`/`add_event`/`add_sejour`/
 * `add_stage`, quasi identiques) regroupées via un paramètre de route
 * `{type}` plutôt que dupliquées 6 fois — même pattern que `ConfigPdfController`.
 */
class ReferentielVacanceController extends Controller
{
    public function index(string $type): View
    {
        $this->validerType($type);

        $items = ReferentielVacance::ofType($type)->orderByDesc('date_debut')->paginate(25);

        return view('referentiel-vacances.index', ['items' => $items, 'type' => $type]);
    }

    public function create(string $type): View
    {
        $this->validerType($type);

        return view('referentiel-vacances.create', [
            'type' => $type,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->validerType($type);

        $data = $this->validerDonnees($request);

        ReferentielVacance::create([
            ...$data,
            'type' => $type,
            'date_creation' => now(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('referentiel-vacances.index', $type)->with('status', 'Créé avec succès.');
    }

    public function edit(string $type, ReferentielVacance $referentielVacance): View
    {
        $this->validerType($type);

        return view('referentiel-vacances.edit', [
            'type' => $type,
            'item' => $referentielVacance,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function update(Request $request, string $type, ReferentielVacance $referentielVacance): RedirectResponse
    {
        $this->validerType($type);

        $referentielVacance->update($this->validerDonnees($request));

        return redirect()->route('referentiel-vacances.index', $type)->with('status', 'Mis à jour.');
    }

    public function destroy(string $type, ReferentielVacance $referentielVacance): RedirectResponse
    {
        $this->validerType($type);

        $referentielVacance->delete();

        return redirect()->route('referentiel-vacances.index', $type)->with('status', 'Supprimé.');
    }

    private function validerType(string $type): void
    {
        abort_unless(in_array($type, ReferentielVacance::TYPES, true), 404);
    }

    private function validerDonnees(Request $request): array
    {
        return $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'id_etablissement' => ['nullable', 'string', 'max:255'],
            'id_classe' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
