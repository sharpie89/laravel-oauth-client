<?php

namespace Sharpie89\LaravelOAuthClient;

use Sharpie89\LaravelOAuthClient\Http\Middleware\OAuthCallback;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oauth-drivers.php', 'oauth-drivers');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app['router']->aliasMiddleware('oauth-callback', OAuthCallback::class);
    }
}
