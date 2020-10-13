<?php

namespace Sharpie89\LaravelOAuthClient\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Sharpie89\LaravelOAuthClient\Models\Token;

trait Tokenizable
{
    public function accessToken(): MorphOne
    {
        return $this->morphOne(Token::class, 'tokenizable');
    }
}
