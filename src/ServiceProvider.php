<?php

namespace Common;

use Common\Channel\StorageChannel;
use Common\Commands\FileDecryptCommand;
use Common\Commands\FileEncryptCommand;
use Common\Commands\RenameMigrationsCommand;
use Common\Middleware\HandleUserImpersonate;
use Common\Middleware\ProtectFromImpersonation;
use Common\Support\Macros;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Macros::boot();

        $this->bootMiddlewares();

        if ($this->app->runningInConsole()) {
            $this->commands([
                FileDecryptCommand::class,
                FileEncryptCommand::class,
                RenameMigrationsCommand::class,
            ]);
        }
    }

    /**
     * Register any module services.
     */
    public function register(): void
    {
        $this->registerStorageDriver();
    }

    /**
     * Register the storage driver.
     */
    private function registerStorageDriver(): void
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('storage', function ($app) {
                return $app->make(StorageChannel::class);
            });
        });
    }

    /**
     * Boot the middlewares for impersonation.
     */
    private function bootMiddlewares(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('impersonate', HandleUserImpersonate::class);
        $router->aliasMiddleware('protect.impersonate', ProtectFromImpersonation::class);
    }
}
