<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserEleve;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage de `User::login()/logout()` + `Login_users_model::validate()`.
 *
 * NON couvert : réinitialisation par email, connexion SSO Office 365, et le
 * mot de passe temporaire imposant une réinitialisation (voir `UserEleve`).
 */
class EleveAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('eleve')->check()) {
            return redirect()->intended(route('eleve.dashboard'));
        }

        return view('auth.eleve-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Le legacy prend le compte le plus récent en cas de doublon d'identifiant (order_by id_eleve DESC limit 1).
        $user = UserEleve::where('username', $credentials['username'])->orderByDesc('id_eleve')->first();

        if (! $user || ! $user->checkPassword($credentials['password'])) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Email et / ou mot de passe invalide']);
        }

        if (! $user->valide) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Votre compte est temporairement verrouillé. Merci de nous contacter.']);
        }

        Auth::guard('eleve')->login($user);
        $user->update(['connexion' => now()]);
        $request->session()->regenerate();

        return redirect()->intended(route('eleve.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('eleve')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('eleve.login');
    }
}
