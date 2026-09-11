<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage minimal de `Entreprises_portail.php` (CodeIgniter) — accueil et
 * fiche d'identité de l'espace entreprise.
 *
 * NON couvert : `my_informations()` (édition complète — contacts, missions,
 * relances, sources, secteurs, élèves en mission...) — dépend entièrement
 * du module Entreprises.php (2267 lignes), non encore migré. Seule la
 * consultation de l'identité de base est portée ici pour l'instant.
 */
class EntreprisePortailController extends Controller
{
    public function index(): View
    {
        $entreprise = Auth::guard('entreprise')->user()->entreprise;

        return view('entreprise.dashboard', ['entreprise' => $entreprise]);
    }

    public function informations(): View
    {
        $entreprise = Auth::guard('entreprise')->user()->entreprise()->with(['etablissement', 'parent', 'filiales'])->first();

        return view('entreprise.informations', ['entreprise' => $entreprise]);
    }

    public function updateInformations(Request $request): RedirectResponse
    {
        $entreprise = Auth::guard('entreprise')->user()->entreprise;

        $data = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:250'],
            'adresse' => ['nullable', 'string'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'ville' => ['nullable', 'string', 'max:20'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:250'],
            'site_web' => ['nullable', 'url', 'max:500'],
        ]);

        $entreprise->update($data);

        return redirect()
            ->route('entreprise.informations')
            ->with('status', 'Informations mises à jour.');
    }
}
