<?php

namespace App\Http\Controllers;

use App\Models\BulletinEleve;
use App\Models\Classe;
use App\Models\Etablissement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage du volet "gestion administrative du bulletin" de `Bulletins.php`
 * (set_bulletin_save / set_bulletin_publication / set_bulletin_publication_all).
 *
 * NON couvert (voir `App\Models\BulletinEleve`) : le calcul des moyennes par
 * UE (`bulletins_generer()`, ~190 lignes), la génération PDF
 * (`pdf()`/`pdf_all()`/`createZip()`) et les exports CSV de session — ce
 * sont des chantiers de reporting/mise en page à part entière.
 */
class BulletinController extends Controller
{
    public function index(Request $request): View
    {
        $bulletins = BulletinEleve::with(['eleve.contact', 'etablissement', 'classe'])
            ->when($request->filled('id_etablissement'), fn ($q) => $q->where('id_etablissement', $request->integer('id_etablissement')))
            ->when($request->filled('id_referentiel'), fn ($q) => $q->where('id_referentiel', $request->integer('id_referentiel')))
            ->when($request->filled('annee'), fn ($q) => $q->where('annee', $request->integer('annee')))
            ->when($request->filled('semestre'), fn ($q) => $q->where('semestre', $request->integer('semestre')))
            ->orderByDesc('date_create')
            ->paginate(25)
            ->withQueryString();

        return view('bulletins.index', [
            'bulletins' => $bulletins,
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
            'classes' => Classe::orderBy('classe')->get(),
        ]);
    }

    /** Portage de `set_bulletin_save()` — enregistre commentaire + décision de jury pour un élève. */
    public function save(Request $request): JsonResponse
    {
        $data = $this->validerDonnees($request, exigerEtudiant: true);

        $bulletin = BulletinEleve::updateOrCreate(
            $this->cle($data),
            [
                'commentaire' => $data['commentaire'] ?? '',
                'decision_jury' => $data['decision_jury'] ?? '',
                'bulletin_json' => $data['bulletin_json'] ?? '',
                'est_publie' => $data['est_publie'] ?? '0',
            ]
        );

        return response()->json($bulletin);
    }

    /** Portage de `set_bulletin_publication()` — même chose + statut de publication pour un élève. */
    public function publish(Request $request): JsonResponse
    {
        $data = $this->validerDonnees($request, exigerEtudiant: true);

        $bulletin = BulletinEleve::updateOrCreate(
            $this->cle($data),
            [
                'commentaire' => $data['commentaire'] ?? '',
                'decision_jury' => $data['decision_jury'] ?? '',
                'est_publie' => $data['est_publie'] ?? '1',
            ]
        );

        return response()->json($bulletin);
    }

    /** Portage de `set_bulletin_publication_all()` / `set_bulletin_generation_all()` — statut pour toute une classe. */
    public function publishAll(Request $request): JsonResponse
    {
        $data = $this->validerDonnees($request, exigerEtudiant: false);

        $bulletin = BulletinEleve::updateOrCreate(
            [
                'id_etablissement' => $data['etablissement'],
                'annee' => $data['annee'],
                'id_referentiel' => $data['classe'],
                'semestre' => $data['semestre'],
                'session' => $data['session'],
                'id_eleve' => 0,
            ],
            ['est_publie' => $data['est_publie'] ?? '1']
        );

        return response()->json($bulletin);
    }

    private function validerDonnees(Request $request, bool $exigerEtudiant): array
    {
        $regles = [
            'etablissement' => ['required', 'integer', 'exists:amos_etablissement,id_etablissement'],
            'annee' => ['required', 'integer'],
            'classe' => ['required', 'integer'],
            'semestre' => ['required', 'integer'],
            'session' => ['required', 'integer'],
            'commentaire' => ['nullable', 'string'],
            'decision_jury' => ['nullable', 'string'],
            'bulletin_json' => ['nullable', 'string'],
            'est_publie' => ['nullable', 'string'],
        ];

        $regles['etudiant'] = $exigerEtudiant ? ['required', 'integer', 'exists:amos_eleves,id_eleve'] : ['nullable', 'integer'];

        return $request->validate($regles);
    }

    private function cle(array $data): array
    {
        return [
            'id_eleve' => $data['etudiant'],
            'id_etablissement' => $data['etablissement'],
            'annee' => $data['annee'],
            'id_referentiel' => $data['classe'],
            'semestre' => $data['semestre'],
            'session' => $data['session'],
        ];
    }
}
