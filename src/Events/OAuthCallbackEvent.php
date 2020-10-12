<?php

namespace Sharpie89\LaravelOAuthClient\Events;

class OAuthCallbackEvent
{
    public string $state;
    public string $code;

    public function __construct(string $state, string $code)
    {
        $this->state = $state;
        $this->code = $code;
    }
}
