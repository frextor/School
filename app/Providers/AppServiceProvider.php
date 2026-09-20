<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vue de pagination sur-mesure (cohérente avec le design du layout partagé)
        // plutôt que le rendu Tailwind par défaut, non stylé faute de Tailwind chargé.
        Paginator::defaultView('pagination.custom');

        // Jours et mois en français dans les écrans qui les écrivent en toutes
        // lettres (calendrier de l'emploi du temps). Seul l'affichage des dates
        // est concerné : la locale applicative reste inchangée.
        Carbon::setLocale('fr');
    }
}
