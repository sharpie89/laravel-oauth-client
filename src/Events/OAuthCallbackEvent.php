<?php

namespace Sharpie89\LaravelOAuthClient\Events;

use Sharpie89\LaravelOAuthClient\Models\OAuthToken;

class OAuthCallbackEvent
{
    public OAuthToken $oauthToken;

    public function __construct(OAuthToken $oauthToken)
    {
        $this->oauthToken = $oauthToken;
    }
}
