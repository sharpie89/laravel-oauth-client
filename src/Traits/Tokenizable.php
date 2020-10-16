<?php

namespace Sharpie89\LaravelOAuthClient\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Sharpie89\LaravelOAuthClient\Models\Client;
use Sharpie89\LaravelOAuthClient\Models\Token;

trait Tokenizable
{
    public function token(): MorphOne
    {
        return $this->morphOne(Token::class, 'tokenizable');
    }

    public function createTokenWith(Client $client): Token
    {
        return $client->tokens()->create([
            'tokenizable_type' => $this->getMorphClass(),
            'tokenizable_id' => $this->getKey()
        ]);;
    }
}
