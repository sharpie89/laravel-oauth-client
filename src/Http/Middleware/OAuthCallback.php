<?php

namespace Sharpie89\LaravelOAuthClient\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sharpie89\LaravelOAuthClient\Models\Token;

class OAuthCallback
{
    public function handle(Request $request, Closure $next)
    {
        $request->validate([
            'state' => [
                'required',
                'string',
                'exists:tokens,state'
            ],
            'code' => [
                'required',
                'string'
            ],
        ]);

        /** @var Token $token */
        $token = Token::query()
            ->state($request->get('state'))
            ->first();

        $token->code = $request->get('code');
        $token->state = null;

        $token->save();

        $request->merge(compact('token'));

        return $next($request);
    }
}
