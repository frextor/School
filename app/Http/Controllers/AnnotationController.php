<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Portage de `Annotations.php` (CodeIgniter).
 *
 * NON couvert : `set_trace_contact()` (journalisation de la fiche contact
 * dans le CRM) — dépend du module Crm/journal, migré séparément.
 */
class AnnotationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer', 'exists:amos_contacts,id_contact'],
            'date' => ['nullable', 'date'],
            'content' => ['required', 'string', 'max:255'],
        ]);

        $annotation = Annotation::create([
            'contact_id' => $data['contact_id'],
            'content' => $data['content'],
            'created_at' => $data['date'] ?? now(),
        ]);

        return response()->json($annotation);
    }

    public function forContact(int $contactId): JsonResponse
    {
        $annotations = Annotation::where('contact_id', $contactId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($annotations);
    }
}
