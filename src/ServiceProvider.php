<?php

namespace Common;

use Common\Channel\StorageChannel;
use Common\Commands\FileDecryptCommand;
use Common\Commands\FileEncryptCommand;
use Common\Commands\RenameMigrationsCommand;
use Common\Support\Macros;
use Illuminate\Notifications\ChannelManager;
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
}
