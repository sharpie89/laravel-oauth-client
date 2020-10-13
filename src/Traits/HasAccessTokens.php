<?php

namespace Sharpie89\LaravelOAuthClient\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Sharpie89\LaravelOAuthClient\Models\OAuthToken;

trait HasAccessTokens
{
    public function accessToken(): HasOne
    {
        return $this->morphOne(OAuthToken::class, 'tokenizable');
    }
}
