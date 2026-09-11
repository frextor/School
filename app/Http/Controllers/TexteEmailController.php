<?php

namespace App\Http\Controllers;

use App\Models\TexteEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Configuration::emails()` (édition des modèles d'emails par
 * catégorie/langue, ex. "fiche_contact", "reset_password"...).
 *
 * NON couvert : `mail_test()`/`sendmail_test()` (envoi réel d'un email de
 * test) — dépend d'un moteur d'envoi non migré.
 */
class TexteEmailController extends Controller
{
    public function index(Request $request): View
    {
        $textes = TexteEmail::query()
            ->when($request->filled('lang'), fn ($q) => $q->where('lang', $request->string('lang')))
            ->orderBy('categorie')
            ->paginate(30);

        return view('emails.index', ['textes' => $textes]);
    }

    public function edit(TexteEmail $email): View
    {
        return view('emails.edit', ['email' => $email]);
    }

    public function update(Request $request, TexteEmail $email): RedirectResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:500'],
            'sujet' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'statut' => ['boolean'],
        ]);

        $email->update([
            'titre' => $data['titre'],
            'sujet' => $data['sujet'],
            'message' => $data['message'],
            'statut' => $request->boolean('statut'),
        ]);

        return redirect()
            ->route('emails.index')
            ->with('status', "Modèle d'email « {$email->categorie} » mis à jour.");
    }
}
