<?php

namespace App\Http\Controllers;

use App\Models\DocumentEleve;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Portage de `Cloud.php` / `Cloud_model.php` (CodeIgniter) — gestion
 * documentaire ("cloud" de fichiers rattachés à un élève, une classe, un
 * niveau ou un établissement).
 *
 * Simplifications assumées par rapport au legacy :
 * - Stockage via `Storage::disk('public')` plutôt que des chemins
 *   `./docs/document_eleve/...` écrits en dur.
 * - La liste ne repasse pas par le contrat JSON DataTables server-side
 *   (voir RoleController pour la même décision).
 * - Un seul document par élève de la liste (le legacy permettait d'associer
 *   le même document à plusieurs élèves en une requête ; ici on crée une
 *   ligne par élève, ce qui est déjà le comportement de `add_documents()`).
 */
class CloudController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $documents = DocumentEleve::query()
            ->with(['eleve.contact', 'classe', 'niveau', 'etablissement'])
            ->when($request->filled('recherche'), fn ($q) => $q->where('titre', 'like', $request->string('recherche').'%'))
            ->orderByDesc('id_document')
            ->paginate(25);

        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'cible' => ['required', 'in:eleve,classe,niveau,etablissement'],
            'id_classe' => ['nullable', 'integer'],
            'id_niveau' => ['nullable', 'integer'],
            'id_etablissement' => ['nullable', 'integer'],
            'eleves' => ['nullable', 'array'],
            'eleves.*' => ['integer', 'exists:amos_eleves,id_eleve'],
            'documents' => ['required', 'array'],
            'documents.*' => ['file', 'mimes:jpg,png,doc,docx,pdf'],
        ]);

        $documentsCrees = [];

        if ($data['cible'] === 'eleve') {
            foreach ($data['eleves'] ?? [] as $idEleve) {
                $documentsCrees[] = $this->creerDocument($data, ['id_eleve' => $idEleve], "document_eleve/eleve/{$idEleve}");
            }
        } else {
            $cibleColonne = 'id_'.$data['cible'];
            $cibleValeur = $data[$cibleColonne] ?? null;

            abort_if(! $cibleValeur, 422, "L'identifiant de la cible ({$cibleColonne}) est requis.");

            $documentsCrees[] = $this->creerDocument($data, [$cibleColonne => $cibleValeur], "document_eleve/{$data['cible']}/{$cibleValeur}");
        }

        return response()->json(['status' => 'ok', 'documents' => $documentsCrees]);
    }

    public function destroy(DocumentEleve $document): JsonResponse
    {
        foreach ($document->fichiers as $fichier) {
            Storage::disk('public')->delete($document->dossierStockage().'/'.$fichier);
        }

        $document->delete();

        return response()->json(['status' => 'ok']);
    }

    /** @param  array<string, mixed>  $data */
    private function creerDocument(array $data, array $cible, string $dossier): DocumentEleve
    {
        $noms = [];

        foreach ($data['documents'] as $fichier) {
            $nomStocke = $fichier->hashName();
            $fichier->storeAs($dossier, $nomStocke, 'public');
            $noms[] = $nomStocke;
        }

        return DocumentEleve::create([
            'id_eleve' => 0,
            'id_classe' => 0,
            'id_niveau' => 0,
            'id_etablissement' => 0,
            'id_intervenant' => 0,
            ...$cible,
            'titre' => $data['titre'],
            'description' => ucfirst($data['cible']),
            'document_eleve' => implode('|', $noms),
            'visible' => true,
        ]);
    }
}
