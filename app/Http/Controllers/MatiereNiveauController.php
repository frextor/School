<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Matières enseignées à un niveau et leur coefficient (K-12).
 *
 * Géré depuis l'écran d'édition d'un niveau, comme le catalogue d'options
 * facturables : un coefficient n'a de sens que rapporté à un niveau.
 */
class MatiereNiveauController extends Controller
{
    public function store(Request $request, Niveau $niveau): RedirectResponse
    {
        $data = $request->validate([
            'id_cours' => ['required', 'integer', 'exists:amos_cours,id_cours'],
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:20'],
            'ordre' => ['nullable', 'integer', 'min:1', 'max:255'],
        ]);

        if ($niveau->matieres()->where('amos_cours.id_cours', $data['id_cours'])->exists()) {
            return back()->withErrors(['id_cours' => 'Cette matière est déjà rattachée à ce niveau.']);
        }

        $niveau->matieres()->attach($data['id_cours'], [
            'coefficient' => $data['coefficient'],
            'ordre' => $data['ordre'] ?? ($niveau->matieres()->count() + 1),
        ]);

        return back()->with('status', 'Matière ajoutée au niveau.');
    }

    public function update(Request $request, Niveau $niveau, int $idCours): RedirectResponse
    {
        $data = $request->validate([
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:20'],
            'ordre' => ['nullable', 'integer', 'min:1', 'max:255'],
        ]);

        $niveau->matieres()->updateExistingPivot($idCours, [
            'coefficient' => $data['coefficient'],
            'ordre' => $data['ordre'] ?? 1,
        ]);

        return back()->with('status', 'Coefficient mis à jour.');
    }

    public function destroy(Niveau $niveau, int $idCours): RedirectResponse
    {
        $niveau->matieres()->detach($idCours);

        return back()->with('status', 'Matière retirée du niveau.');
    }
}
