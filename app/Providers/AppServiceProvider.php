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

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            \App\Models\AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'target_type' => get_class($event->user),
                'target_id' => $event->user->id,
                'metadata' => [
                    'target_name' => $event->user->name,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
            ]);
        });
    }
}
