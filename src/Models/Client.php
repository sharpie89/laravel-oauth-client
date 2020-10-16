<?php

namespace Sharpie89\LaravelOAuthClient\Models;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Client\Providers\Provider;

/**
 * @property-read Provider provider
 * @property-read array provider_config
 * @property array provider_options
 * @property array client_options
 * @property string driver
 */
class Client extends Model
{
    protected $fillable = [
        'driver',
        'client_options',
        'provider_options',
    ];

    protected $casts = [
        'client_options' => 'array',
        'provider_options' => 'array'
    ];

    public static function booted()
    {
        static::retrieved(function (self $client) {
            $httpClient = new HttpClient($client->client_options);
            $client->attributes['provider'] = new Provider($client->provider_config, compact('httpClient'));
        });
    }

    public function getProviderConfigAttribute(): array
    {
        // Priority on the provider configurations:
        // 1: provider_options column
        // 2: driver_config from oauth-drivers config file
        // 3: the "default" config from oauth-drivers config file
        // 4: $requiredOptions

        $requiredOptions = [
            'urlAuthorize' => 'login/oauth/authorize',
            'urlAccessToken' => 'login/oauth/access_token',
            'urlResourceOwnerDetails' => 'api/v1/user',
            'redirectUri' => url('oauth/callback'),
        ];

        return array_merge(
            $requiredOptions,
            config("oauth-drivers.default", []),
            config("oauth-drivers.{$this->driver}", []),
            $this->provider_options
        );
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
        return $this->hasMany(Token::class);
    }
}
