<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Signature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Portage de la gestion des signatures dans `Referentiel.php`
 * (add_signature / get_signature / supp_signature / update_signature / valide_update_signature).
 */
class SignatureController extends Controller
{
    public function index(): View
    {
        $signatures = Signature::with('etablissement')->orderBy('nom_directeur')->paginate(25);

        return view('referentiel.signatures.index', ['signatures' => $signatures]);
    }

    public function create(): View
    {
        return view('referentiel.signatures.create', ['etablissements' => Etablissement::orderBy('nom_etablissement')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'civilite' => ['required', 'string', 'max:5'],
            'nom_directeur' => ['required', 'string', 'max:70'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'fonction' => ['required', 'string', 'max:150'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
        ]);

        $nomFichier = '';
        if ($request->hasFile('signature')) {
            $nomFichier = $request->file('signature')->hashName();
            $request->file('signature')->storeAs('signatures', $nomFichier, 'public');
        }

        $signature = Signature::create([
            'civilite' => $data['civilite'],
            'nom_directeur' => $data['nom_directeur'],
            'id_etablissement' => $data['id_etablissement'],
            'fonction' => $data['fonction'],
            'signature' => $nomFichier,
            'principal' => false,
            'date_creation' => now(),
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('referentiel.signatures.index')
            ->with('status', "Signature de {$signature->nom_directeur} créée avec succès.");
    }

    public function edit(Signature $signature): View
    {
        return view('referentiel.signatures.edit', [
            'signature' => $signature,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function update(Request $request, Signature $signature): RedirectResponse
    {
        $data = $request->validate([
            'civilite' => ['required', 'string', 'max:5'],
            'nom_directeur' => ['required', 'string', 'max:70'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'fonction' => ['required', 'string', 'max:150'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
            'supprimer_signature' => ['boolean'],
        ]);

        $nomFichier = $signature->signature;

        if ($request->boolean('supprimer_signature') && $chemin = $signature->cheminFichier()) {
            Storage::disk('public')->delete($chemin);
            $nomFichier = '';
        } elseif ($request->hasFile('signature')) {
            if ($chemin = $signature->cheminFichier()) {
                Storage::disk('public')->delete($chemin);
            }
            $nomFichier = $request->file('signature')->hashName();
            $request->file('signature')->storeAs('signatures', $nomFichier, 'public');
        }

        $signature->update([
            'civilite' => $data['civilite'],
            'nom_directeur' => $data['nom_directeur'],
            'id_etablissement' => $data['id_etablissement'],
            'fonction' => $data['fonction'],
            'signature' => $nomFichier,
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('referentiel.signatures.index')
            ->with('status', "Signature de {$signature->nom_directeur} mise à jour.");
    }

    public function destroy(Signature $signature): RedirectResponse
    {
        if ($chemin = $signature->cheminFichier()) {
            Storage::disk('public')->delete($chemin);
        }

        $signature->delete();

        return redirect()
            ->route('referentiel.signatures.index')
            ->with('status', 'Signature supprimée.');
    }
}
