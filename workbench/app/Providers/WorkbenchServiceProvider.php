<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('canImpersonate', function ($user) {
            return in_array($user->email, ['admin@example.com']);
        });

        Gate::define('canBeImpersonated', function ($user) {
            return ! in_array($user->email, ['admin@example.com']);
        });
    }
}
