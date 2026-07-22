<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Gate::define('is-admin', fn($user) => $user->role === 'admin');
        \Illuminate\Support\Facades\Gate::define('is-manager', fn($user) => $user->role === 'manager');
        \Illuminate\Support\Facades\Gate::define('is-employee', fn($user) => $user->role === 'employee');
        
        \Illuminate\Support\Facades\Gate::define('view-dashboard', fn($user) => in_array($user->role, ['admin', 'manager']));
        \Illuminate\Support\Facades\Gate::define('view-operations', fn($user) => in_array($user->role, ['admin', 'manager', 'employee']));
        \Illuminate\Support\Facades\Gate::define('edit-operations', fn($user) => in_array($user->role, ['admin', 'employee']));
    }
}
