<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Tache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage du sous-système "tâches CRM" / relances de `Crm.php`
 * (nouvelle_tache / rappel_immediat / cloture_tache).
 *
 * Simplifications et exclusions assumées :
 * - **Aucune synchronisation Mautic** (`sync_mautic`) — exclue à la demande
 *   de l'utilisateur.
 * - `actions_crm_model->log_action()` (journal détaillé de la fiche contact,
 *   table `amos_contact_origine_traces`) et `watch()` (trace admin) ne sont
 *   pas portés : c'est un système de journalisation à part entière, hors
 *   périmètre d'un CRUD de tâches.
 * - `stats_library->plus_objectifs()` (comptage d'objectifs commerciaux
 *   par admin) non porté : dépend d'un module de reporting non migré.
 */
class TacheController extends Controller
{
    public function index(Request $request): View
    {
        $taches = Tache::with(['contact', 'statut', 'type'])
            ->ouvertes()
            ->when($request->filled('id_contact'), fn ($q) => $q->where('id_contact', $request->integer('id_contact')))
            ->orderByDesc('deadline')
            ->paginate(25)
            ->withQueryString();

        return view('taches.index', ['taches' => $taches]);
    }

    /** Portage de `nouvelle_tache()` / `rappel_immediat()` (unifiés : la différence n'était que l'échéance par défaut). */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_contact' => ['required', 'integer', 'exists:amos_contacts,id_contact'],
            'id_service_assigne' => ['nullable', 'integer'],
            'commentaire' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'id_type_tache' => ['nullable', 'integer', 'exists:amos_types_taches,id_type_tache'],
        ]);

        $tache = Tache::create([
            'id_contact' => $data['id_contact'],
            'id_service_assigne' => $data['id_service_assigne'] ?? null,
            'commentaire' => $data['commentaire'] ?? '',
            'id_statut_contact' => 20,
            'deadline' => $data['deadline'] ?? now(),
            'id_type_tache' => $data['id_type_tache'] ?? 0,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Tâche créée', 'id_tache' => $tache->id_tache]);
    }

    /** Portage de `cloture_tache()` — abandon du contact (arrête les relances). */
    public function close(Request $request, Tache $tache): JsonResponse
    {
        $data = $request->validate(['id_motif' => ['nullable', 'integer']]);

        $tache->update(['archive' => true]);

        Contact::where('id_contact', $tache->id_contact)->update([
            'stop_relances' => true,
            'id_motif_abandon' => $data['id_motif'] ?? null,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Tâche clôturée']);
    }
}
