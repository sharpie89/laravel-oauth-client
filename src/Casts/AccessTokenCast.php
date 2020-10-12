<?php

namespace Sharpie89\LaravelOAuthClient\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Models\OAuthProvider;

class AccessTokenCast implements CastsAttributes
{
    /**
     * @param  OAuthProvider  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return AccessTokenInterface
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new AccessToken($value);
    }

    /**
     * @param  OAuthProvider  $model
     * @param  string  $key
     * @param  AccessTokenInterface  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value->jsonSerialize();
    }
}
