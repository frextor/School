<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Plusieurs back-offices distincts (admin / intervenant / entreprise) :
        // on redirige vers le bon écran de connexion selon le préfixe de route.
        if ($request->routeIs('recapitulatif.*') || $request->routeIs('intervenant.*')) {
            return route('intervenant.login');
        }

        if ($request->routeIs('entreprise.*')) {
            return route('entreprise.login');
        }

        if ($request->routeIs('eleve.*')) {
            return route('eleve.login');
        }

        return route('admin.login');
    }
}
