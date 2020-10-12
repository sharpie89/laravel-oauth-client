<?php

namespace Sharpie89\LaravelOAuthClient\Listeners;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Sharpie89\LaravelOAuthClient\Events\OAuthCallbackEvent;
use Sharpie89\LaravelOAuthClient\Models\OAuthProvider;

class OAuthCallbackListener
{
    public function handle(OAuthCallbackEvent $event): void
    {
        /** @var OAuthProvider $oauthProvider */
        $oauthProvider = OAuthProvider::query()
            ->where('state', $event->state)
            ->first();

        try {
            $oauthProvider->authenticateByCode($event->code);
        } catch (IdentityProviderException $e) {
            //
        }
    }
}
