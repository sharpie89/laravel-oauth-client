<?php

namespace Sharpie89\LaravelOAuthClient;

use Sharpie89\LaravelOAuthClient\Events\OAuthCallbackEvent;
use Sharpie89\LaravelOAuthClient\Listeners\OAuthCallbackListener;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        OAuthCallbackEvent::class => [
            OAuthCallbackListener::class
        ]
    ];
}
