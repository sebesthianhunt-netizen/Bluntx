<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('admin', function ($user) {
            return in_array($user->role ?? 'User', ['SuperAdmin', 'Admin']);
        });

        Gate::define('attendant', function ($user) {
            return in_array($user->role ?? 'User', ['SuperAdmin', 'Admin', 'Attendant']);
        });

        Gate::define('agent', function ($user) {
            return in_array($user->role ?? 'User', ['SuperAdmin', 'Admin', 'Agent']);
        });
    }
}


