<?php

namespace Sharpie89\LaravelOAuthClient\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sharpie89\LaravelOAuthClient\Events\OAuthCallbackEvent;
use Sharpie89\LaravelOAuthClient\Models\OAuthToken;

class OAuthCallback
{
    public function handle(Request $request, Closure $next)
    {
        $request->validate([
            'state' => [
                'required',
                'string',
                'exists:oauth_tokens,state'
            ],
            'code' => [
                'required',
                'string'
            ],
        ]);

        /** @var OAuthToken $oauthToken */
        $oauthToken = OAuthToken::query()
            ->state($request->get('state'))
            ->first();

        $oauthToken->code = $request->get('code');

        $oauthToken->save();

        event(new OAuthCallbackEvent($oauthToken));

        return $next($request);
    }
}
