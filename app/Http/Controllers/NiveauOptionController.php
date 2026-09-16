<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use App\Models\NiveauxOptions;
use App\Models\ObjetPaiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Portage du CRUD des options facturables par niveau (`niveau_option_model`,
 * table `amos_niveaux_options`) — géré depuis l'écran d'édition d'un niveau.
 * Ces options sont ensuite proposées (avec leur montant, modifiable au cas
 * par cas) lors de la création d'un règlement élève, voir `PaiementController`.
 */
class NiveauOptionController extends Controller
{
    public function store(Request $request, Niveau $niveau): RedirectResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:64'],
            'id_objet_paiement' => ['required', 'integer', 'exists:amos_objet_paiement,id_objet_paiement'],
            'montant' => ['required', 'numeric', 'min:0'],
            'periodicite' => ['required', 'in:annuelle,mensuelle'],
            'annee' => ['required', 'integer'],
            'ordre' => ['nullable', 'integer'],
        ]);

        NiveauxOptions::create([
            'id_niveau' => $niveau->id_niveau,
            'id_objet_paiement' => $data['id_objet_paiement'],
            'titre' => $data['titre'],
            'montant' => $data['montant'],
            'periodicite' => $data['periodicite'],
            'annee' => $data['annee'],
            'ordre' => $data['ordre'] ?? 0,
        ]);

        return redirect()
            ->route('referentiel.niveaux.edit', $niveau)
            ->with('status', 'Option ajoutée au catalogue du niveau.');
    }

    public function update(Request $request, Niveau $niveau, NiveauxOptions $option): RedirectResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:64'],
            'id_objet_paiement' => ['required', 'integer', 'exists:amos_objet_paiement,id_objet_paiement'],
            'montant' => ['required', 'numeric', 'min:0'],
            'periodicite' => ['required', 'in:annuelle,mensuelle'],
            'annee' => ['required', 'integer'],
            'ordre' => ['nullable', 'integer'],
        ]);

        $option->update($data);

        return redirect()
            ->route('referentiel.niveaux.edit', $niveau)
            ->with('status', 'Option mise à jour.');
    }

    public function destroy(Niveau $niveau, NiveauxOptions $option): RedirectResponse
    {
        $option->delete();

        return redirect()
            ->route('referentiel.niveaux.edit', $niveau)
            ->with('status', 'Option supprimée du catalogue.');
    }

    /** Utilisé par les formulaires (catalogue "objet de paiement" — comptabilité, non géré ici). */
    public static function objetsPaiement()
    {
        return ObjetPaiement::orderBy('objet_paiement')->get();
    }
}
