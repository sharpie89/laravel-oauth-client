<?php

namespace Sharpie89\LaravelOAuthClient\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Sharpie89\LaravelOAuthClient\Models\Token;

trait Tokenizable
{
    public function accessToken(): HasOne
    {
        return $this->morphOne(Token::class, 'tokenizable');
    }
}
