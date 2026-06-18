<?php

namespace Insynnia\Badges;

use Illuminate\Support\ServiceProvider;

class BadgesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/badges.php', 'badges');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/badges.php' => config_path('badges.php'),
            ], 'badges-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'badges-migrations');
        }
    }
}
