<?php

namespace Sharpie89\LaravelOAuthClient\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Models\Token;

class AccessTokenCast implements CastsAttributes
{
    /**
     * @param  Token  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return AccessTokenInterface
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new AccessToken(json_decode($value));
    }

    /**
     * @param  Token  $model
     * @param  string  $key
     * @param  AccessTokenInterface  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value->jsonSerialize());
    }
}
