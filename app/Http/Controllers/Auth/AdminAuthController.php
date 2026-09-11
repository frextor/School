<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portage de `Admin::login()` / `Admin::logout()` + `Login_model::validate()`
 * (CodeIgniter). Le mot de passe legacy est stocké en md5 sans salt : voir
 * App\Models\Admin::checkPassword().
 */
class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('username', $credentials['username'])->first();

        if (! $admin || ! $admin->checkPassword($credentials['password'])) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "Nom d'utilisateur et / ou mot de passe invalide"]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
