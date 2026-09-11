<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserIntervenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage de `User_intervenant::login()/logout()` + `Login_users_intervenants_model::validate()`.
 *
 * NON couvert : réinitialisation de mot de passe par email (`reset`,
 * `add_recapitulatif` avec token) et connexion SSO Office 365
 * (`office_sso_login`) — dépendent respectivement du moteur d'emails et
 * d'une intégration OAuth Azure AD, tous deux hors périmètre ici.
 */
class IntervenantAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('intervenant')->check()) {
            return redirect()->intended(route('intervenant.dashboard'));
        }

        return view('auth.intervenant-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = UserIntervenant::where('username', $credentials['username'])->first();

        if (! $user || ! $user->checkPassword($credentials['password'])) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Email et / ou mot de passe invalide']);
        }

        if (! $user->valide) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "Ce compte n'est pas encore validé."]);
        }

        Auth::guard('intervenant')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('intervenant.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('intervenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('intervenant.login');
    }
}
