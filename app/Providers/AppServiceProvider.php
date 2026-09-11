<?php

namespace App\Providers;

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
    }
}
