<?php

namespace Sharpie89\LaravelOAuthClient;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oauth-drivers.php', 'oauth-drivers');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
