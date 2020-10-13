<?php

namespace Sharpie89\LaravelOAuthClient\Events;

use Sharpie89\LaravelOAuthClient\Models\Token;

class OAuthCallbackEvent
{
    public Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }
}
