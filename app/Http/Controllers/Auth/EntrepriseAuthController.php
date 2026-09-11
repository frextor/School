<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EntreprisePortail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage de `Entreprises_portail::login()/logout()` +
 * `Entreprises_portail_model::validate()`.
 *
 * NON couvert : réinitialisation de mot de passe par email (`reset`) —
 * dépend du moteur d'emails, non migré.
 */
class EntrepriseAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('entreprise')->check()) {
            return redirect()->intended(route('entreprise.dashboard'));
        }

        return view('auth.entreprise-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $compte = EntreprisePortail::where('username', $credentials['username'])->first();

        if (! $compte || ! $compte->checkPassword($credentials['password'])) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Email et / ou mot de passe invalide']);
        }

        if (! $compte->valide) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Votre compte a été suspendu, merci de nous contacter pour le réactiver.']);
        }

        Auth::guard('entreprise')->login($compte);
        $request->session()->regenerate();

        return redirect()->intended(route('entreprise.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('entreprise')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('entreprise.login');
    }
}
