<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Definisikan Gate untuk peran admin dan kasir
        Gate::define('admin-kasir', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('kasir');
        });
    }
}
