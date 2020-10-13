<?php

namespace Sharpie89\LaravelOAuthClient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Client\Providers\GenericOAuthProvider;

/**
 * @property GenericOAuthProvider provider
 */
class OAuthClient extends Model
{
    protected $fillable = [
        'driver',
        'url',
        'client_id',
        'client_secret',
    ];

    public static function booted()
    {
        static::retrieved(function (self $oauthClient) {
            $oauthClient->attributes['provider'] = new GenericOAuthProvider([
                'driver' => $oauthClient->driver,
                'url' => $oauthClient->url,
                'clientId' => $oauthClient->client_id,
                'clientSecret' => $oauthClient->client_secret,
            ]);
        });
    }

    /**
     * @param  string  $grant
     * @param  array  $options
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     */
    public function authenticate(string $grant, array $options): AccessTokenInterface
    {
        return $this->provider->getAccessToken($grant, $options);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(OAuthToken::class);
    }
}
