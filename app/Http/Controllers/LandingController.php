<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Page d'accueil publique (vitrine) de l'application, avec les liens vers
 * les différents espaces de connexion (admin, intervenant, élève,
 * entreprise) et l'auto-inscription intervenant. Remplace la redirection
 * brute `/` -> `/admin` d'origine.
 */
class LandingController extends Controller
{
    public function index(): View
    {
        return view('landing');
    }
}
