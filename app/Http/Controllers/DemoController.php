<?php

namespace App\Http\Controllers;

use App\Models\DemandeDemo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Formulaire de demande de démo sur la landing page (resources/views/landing.blade.php).
 * Fonctionnalité nouvelle, sans équivalent legacy — le bouton pointait vers un
 * `Route::has('demo.store') ? ... : '#'` de repli dans le design fourni.
 */
class DemoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'etablissement' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        DemandeDemo::create($data);

        return redirect(route('landing').'#demo')
            ->with('demande_demo', true);
    }
}
