<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Signature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Portage de la gestion des signatures dans `Referentiel.php`
 * (add_signature / get_signature / supp_signature / update_signature / valide_update_signature).
 *
 * La colonne `principal` désigne la signature apposée par défaut sur les
 * documents d'un établissement. Le legacy la portait sans jamais l'exposer :
 * `Functions::get_id_signature_directeur()` retrouvait le directeur par son
 * nom écrit en dur dans le code, avec un commentaire « à modifier par
 * école ». L'écran la règle désormais, à raison d'une seule par
 * établissement.
 */
class SignatureController extends Controller
{
    public function index(): View
    {
        $signatures = Signature::with('etablissement')
            ->orderByDesc('principal')
            ->orderBy('nom_directeur')
            ->paginate(24);

        return view('referentiel.signatures.index', ['signatures' => $signatures]);
    }

    public function create(): View
    {
        return view('referentiel.signatures.create', [
            'signature' => new Signature(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->valider($request);

        $nomFichier = '';
        if ($request->hasFile('signature')) {
            $nomFichier = $request->file('signature')->hashName();
            $request->file('signature')->storeAs('signatures', $nomFichier, 'public');
        }

        try {
            $signature = DB::transaction(function () use ($data, $nomFichier) {
                $signature = Signature::create([
                    'civilite' => $data['civilite'],
                    'nom_directeur' => $data['nom_directeur'],
                    'id_etablissement' => $data['id_etablissement'],
                    'fonction' => $data['fonction'],
                    'signature' => $nomFichier,
                    'principal' => (bool) ($data['principal'] ?? false),
                    'date_creation' => now(),
                    'date_modification' => now(),
                ]);

                if ($signature->principal) {
                    $this->detronerLesAutres($signature);
                }

                return $signature;
            });
        } catch (\Throwable $erreur) {
            // Le fichier est déjà sur le disque quand l'insertion échoue :
            // sans ce nettoyage il resterait orphelin, sans ligne pour le citer.
            if ($nomFichier) {
                Storage::disk('public')->delete('signatures/'.$nomFichier);
            }

            throw $erreur;
        }

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
        $data = $this->valider($request);

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

        DB::transaction(function () use ($signature, $data, $nomFichier) {
            $signature->update([
                'civilite' => $data['civilite'],
                'nom_directeur' => $data['nom_directeur'],
                'id_etablissement' => $data['id_etablissement'],
                'fonction' => $data['fonction'],
                'signature' => $nomFichier,
                'principal' => (bool) ($data['principal'] ?? false),
                'date_modification' => now(),
            ]);

            if ($signature->principal) {
                $this->detronerLesAutres($signature);
            }
        });

        return redirect()
            ->route('referentiel.signatures.index')
            ->with('status', "Signature de {$signature->nom_directeur} mise à jour.");
    }

    /** Désigne la signature principale depuis la liste, en un clic. */
    public function principale(Signature $signature): RedirectResponse
    {
        DB::transaction(function () use ($signature) {
            $signature->update(['principal' => true, 'date_modification' => now()]);
            $this->detronerLesAutres($signature);
        });

        $ecole = $signature->etablissement?->nom_etablissement;

        return back()->with('status', $ecole
            ? "{$signature->nom_directeur} signe désormais les documents de {$ecole}."
            : "{$signature->nom_directeur} est la signature principale.");
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

    private function valider(Request $request): array
    {
        return $request->validate([
            'civilite' => ['required', 'in:M,Mme'],
            'nom_directeur' => ['required', 'string', 'max:70'],
            'id_etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'fonction' => ['required', 'string', 'max:150'],
            // `png` en plus du `jpg` du legacy : une signature scannée sur fond
            // transparent s'appose proprement sur un document, pas un JPEG.
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'principal' => ['nullable', 'boolean'],
            'supprimer_signature' => ['nullable', 'boolean'],
        ]);
    }

    /** Une seule signature principale par établissement. */
    private function detronerLesAutres(Signature $signature): void
    {
        Signature::where('id_etablissement', $signature->id_etablissement)
            ->where('id_signature', '!=', $signature->id_signature)
            ->where('principal', true)
            ->update(['principal' => false, 'date_modification' => now()]);
    }
}
