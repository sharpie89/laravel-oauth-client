<?php

namespace Sharpie89\LaravelOAuthClient\Http\Middleware;

use Closure;
use Sharpie89\LaravelOAuthClient\Events\OAuthCallbackEvent;
use Sharpie89\LaravelOAuthClient\Http\Requests\OAuthCallbackRequest;

class OAuthCallback
{
    public function handle(OAuthCallbackRequest $request, Closure $next)
    {
        event(new OAuthCallbackEvent($request->get('state'), $request->get('code')));

        return $next($request);
    }
}
