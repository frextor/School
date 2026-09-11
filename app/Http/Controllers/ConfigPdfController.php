<?php

namespace App\Http\Controllers;

use App\Models\ConfigPdf;
use App\Models\Etablissement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Portage unifié de `Referentiel.php` : gestion des configurations PDF
 * (get_configs_pdf* / add_config_pdf* / update_config_pdf* / supp_config_pdf*)
 * — 3 variantes legacy quasi identiques (facture / avoir / attestation),
 * regroupées ici via un paramètre de route `{type}` plutôt que dupliquées
 * en 3 controllers séparés.
 */
class ConfigPdfController extends Controller
{
    private const TYPES = [ConfigPdf::TYPE_FACTURE, ConfigPdf::TYPE_AVOIR, ConfigPdf::TYPE_ATTESTATION];

    public function index(string $type): View
    {
        $this->validerType($type);

        $configs = ConfigPdf::ofType($type)->with('etablissements')->paginate(25);

        return view('referentiel.config-pdf.index', ['configs' => $configs, 'type' => $type]);
    }

    public function create(string $type): View
    {
        $this->validerType($type);

        return view('referentiel.config-pdf.create', [
            'type' => $type,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->validerType($type);

        $data = $this->validerDonnees($request, $type, logoObligatoire: true);

        $config = ConfigPdf::create([
            'logo' => $this->stockerLogo($request),
            'texte_attestation' => nl2br($data['texte_attestation'] ?? ''),
            'texte_facture' => nl2br($data['texte_facture'] ?? ''),
            'texte_facture_contrat_apprentissage' => nl2br($data['texte_facture_contrat_apprentissage'] ?? ''),
            'texte_facture_contrat_pro' => nl2br($data['texte_facture_contrat_pro'] ?? ''),
            'footer_initial' => nl2br($data['footer_initial'] ?? ''),
            'footer_apprentissage' => nl2br($data['footer_apprentissage'] ?? ''),
            'footer_professionnalisation' => nl2br($data['footer_professionnalisation'] ?? ''),
            'type' => $type,
        ]);

        $config->etablissements()->sync($data['id_etablissement'] ?? []);

        return redirect()
            ->route('referentiel.config-pdf.index', $type)
            ->with('status', 'Configuration PDF créée avec succès.');
    }

    public function edit(string $type, ConfigPdf $configPdf): View
    {
        $this->validerType($type);
        $configPdf->load('etablissements');

        return view('referentiel.config-pdf.edit', [
            'type' => $type,
            'config' => $configPdf,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function update(Request $request, string $type, ConfigPdf $configPdf): RedirectResponse
    {
        $this->validerType($type);

        $data = $this->validerDonnees($request, $type);

        $config = [
            'texte_attestation' => nl2br($data['texte_attestation'] ?? ''),
            'texte_facture' => nl2br($data['texte_facture'] ?? ''),
            'texte_facture_contrat_apprentissage' => nl2br($data['texte_facture_contrat_apprentissage'] ?? ''),
            'texte_facture_contrat_pro' => nl2br($data['texte_facture_contrat_pro'] ?? ''),
            'footer_initial' => nl2br($data['footer_initial'] ?? ''),
            'footer_apprentissage' => nl2br($data['footer_apprentissage'] ?? ''),
            'footer_professionnalisation' => nl2br($data['footer_professionnalisation'] ?? ''),
        ];

        if ($request->hasFile('logo')) {
            if ($configPdf->logo) {
                Storage::disk('public')->delete("logo_etablissements/{$configPdf->logo}");
            }
            $config['logo'] = $this->stockerLogo($request);
        }

        $configPdf->update($config);
        $configPdf->etablissements()->sync($data['id_etablissement'] ?? []);

        return redirect()
            ->route('referentiel.config-pdf.index', $type)
            ->with('status', 'Configuration PDF mise à jour.');
    }

    public function destroy(string $type, ConfigPdf $configPdf): RedirectResponse
    {
        $this->validerType($type);

        if ($configPdf->logo) {
            Storage::disk('public')->delete("logo_etablissements/{$configPdf->logo}");
        }

        $configPdf->etablissements()->detach();
        $configPdf->delete();

        return redirect()
            ->route('referentiel.config-pdf.index', $type)
            ->with('status', 'Configuration PDF supprimée.');
    }

    private function validerType(string $type): void
    {
        abort_unless(in_array($type, self::TYPES, true), 404);
    }

    private function validerDonnees(Request $request, string $type, bool $logoObligatoire = false): array
    {
        $regles = [
            'footer_initial' => ['required', 'string'],
            'id_etablissement' => ['array'],
            'id_etablissement.*' => ['integer', 'exists:amos_etablissement,id_etablissement'],
            'logo' => [$logoObligatoire ? 'required' : 'nullable', 'image', 'max:2048'],
        ];

        if ($type === ConfigPdf::TYPE_ATTESTATION) {
            $regles['texte_attestation'] = ['required', 'string'];
        } else {
            $regles['texte_facture'] = ['required', 'string'];
            $regles['texte_facture_contrat_apprentissage'] = ['nullable', 'string'];
            $regles['texte_facture_contrat_pro'] = ['nullable', 'string'];
            $regles['footer_apprentissage'] = ['nullable', 'string'];
            $regles['footer_professionnalisation'] = ['nullable', 'string'];
        }

        return $request->validate($regles);
    }

    private function stockerLogo(Request $request): string
    {
        $fichier = $request->file('logo');
        $nom = Str::slug(pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME)).'-'.$fichier->hashName();
        $fichier->storeAs('logo_etablissements', $nom, 'public');

        return $nom;
    }
}
