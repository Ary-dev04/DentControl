<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Usuario;

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
        // Definición de permisos (Gates)
        
        Gate::define('admin-only', function ($user) {
            return $user->rol === 'superadmin';
        });

        Gate::define('dentista-only', function ($user) {
            return $user->rol === 'dentista';
        });

        Gate::define('asistente-only', function ($user) {
            return $user->rol === 'asistente';
        });
    
    }
}
