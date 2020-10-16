<?php

namespace Sharpie89\LaravelOAuthClient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Client\Providers\Provider;

/**
 * @property Provider provider
 * @property array default_provider_config
 */
class Client extends Model
{
    protected $fillable = [
        'driver',
        'url',
        'client_id',
        'client_secret',
    ];

    public static function booted()
    {
        static::retrieved(function (self $client) {
            $drivers = config('oauth-drivers');

            $client->attributes['provider'] = new Provider([
                'driver' => $client->driver,
                'clientId' => $client->client_id,
                'clientSecret' => $client->client_secret,
                'redirectUri' => url('oauth/callback'),
            ], [
                'httpClient' => new \GuzzleHttp\Client([
                    'base_uri' => $client->url,
                ]),
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

    public function getProviderConfig(): array
    {
        return config("oauth.drivers.{$this->driver}", $this->default_provider_config);
    }

    public function getDefaultProviderConfigAttribute(): array
    {
        return config("oauth.drivers.default", [
            'urlAuthorize' => 'login/oauth/authorize',
            'urlAccessToken' => 'login/oauth/access_token',
            'urlResourceOwnerDetails' => 'api/v4/user'
        ]);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }
}
