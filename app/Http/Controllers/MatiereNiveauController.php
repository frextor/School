<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Matières enseignées à un niveau, leur coefficient et leur volume
 * horaire hebdomadaire (K-12).
 *
 * Rattachement et retrait d'une matière, depuis la fiche d'un niveau comme
 * depuis l'écran « Référentiel pédagogique » : les deux reviennent sur la
 * page d'appel (`back()`). Le réglage en masse des coefficients et des
 * heures d'un niveau passe par `ReferentielController::enregistrer()`.
 */
class MatiereNiveauController extends Controller
{
    public function store(Request $request, Niveau $niveau): RedirectResponse
    {
        $data = $request->validate([
            'id_cours' => ['required', 'integer', 'exists:amos_cours,id_cours'],
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:20'],
            'volume_horaire' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'ordre' => ['nullable', 'integer', 'min:1', 'max:255'],
        ]);

        if ($niveau->matieres()->where('amos_cours.id_cours', $data['id_cours'])->exists()) {
            return back()->withErrors(['id_cours' => 'Cette matière est déjà rattachée à ce niveau.']);
        }

        $niveau->matieres()->attach($data['id_cours'], [
            'coefficient' => $data['coefficient'],
            'volume_horaire' => $data['volume_horaire'] ?? 0,
            'ordre' => $data['ordre'] ?? ($niveau->matieres()->count() + 1),
        ]);

        return back()->with('status', 'Matière ajoutée au niveau.');
    }

    public function update(Request $request, Niveau $niveau, int $idCours): RedirectResponse
    {
        $data = $request->validate([
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:20'],
            'volume_horaire' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'ordre' => ['nullable', 'integer', 'min:1', 'max:255'],
        ]);

        $pivot = [
            'coefficient' => $data['coefficient'],
            'ordre' => $data['ordre'] ?? 1,
        ];

        // La fiche d'un niveau règle le coefficient sans parler des heures :
        // les écraser à 0 effacerait en silence la grille horaire saisie
        // depuis le référentiel. On n'écrit la colonne que si elle est envoyée.
        if ($request->has('volume_horaire')) {
            $pivot['volume_horaire'] = $data['volume_horaire'] ?? 0;
        }

        $niveau->matieres()->updateExistingPivot($idCours, $pivot);

        return back()->with('status', 'Coefficient mis à jour.');
    }

    public function destroy(Niveau $niveau, int $idCours): RedirectResponse
    {
        $niveau->matieres()->detach($idCours);

        return back()->with('status', 'Matière retirée du niveau.');
    }
}
