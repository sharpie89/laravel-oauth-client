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
 * @property-read HttpClient http_client
 * @property-read array provider_config
 * @property array provider_options
 * @property array client_options
 * @property string driver
 * @property-write string client_id
 * @property-write string client_secret
 * @property-write string redirect_uri
 * @property-write string state
 * @property-write string access_token_method
 * @property-write string access_token_resource_owner_id
 * @property-write string scope_separator
 * @property-write string response_error
 * @property-write string response_code
 * @property-write string response_resource_owner_id
 * @property-write string scopes
 * @property-write string url_authorize
 * @property-write string url_access_token
 * @property-write string url_resource_owner_details
 * @property-write string base_uri
 */
class Client extends Model
{
    private Provider $provider;

    protected $fillable = [
        'driver',
        'client_options',
        'provider_options',
    ];

    protected $attributes = [
        'client_options' => '{}',
        'provider_options' => '{}'
    ];

    protected $casts = [
        'client_options' => 'array',
        'provider_options' => 'array'
    ];

    public static function booted()
    {
        static::retrieved(function (self $client) {
            $client->initializeProvider();
        });
        static::saved(function (self $client) {
            $client->initializeProvider();
        });
    }

    public function initializeProvider(): void
    {
        $this->provider = new Provider($this->provider_config, ['httpClient' => $this->http_client]);
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

    // Priority on the provider configurations:
    // 1: provider_options column
    // 2: driver_config from oauth-drivers config file
    // 3: the "default" config from oauth-drivers config file
    // 4: $requiredOptions
    public function getProviderConfigAttribute(): array
    {
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

    public function createTokenFor(Model $model): Token
    {
        return $this->tokens()->create([
            'tokenizable_type' => $model->getMorphClass(),
            'tokenizable_id' => $model->getKey()
        ]);;
    }

    public function getHttpClientAttribute(): HttpClient
    {
        return new HttpClient($this->client_options);
    }

    public function getProviderAttribute(): Provider
    {
        return $this->provider;
    }

    // Provider attributes
    public function setClientIdAttribute(string $clientId): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('clientId'));
    }

    public function setClientSecretAttribute(string $clientSecret): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('clientSecret'));
    }

    public function setRedirectUriAttribute(string $redirectUri): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('redirectUri'));
    }

    public function setUrlAuthorizeAttribute(string $urlAuthorize): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('urlAuthorize'));
    }

    public function setUrlAccessTokenAttribute(string $urlAccessToken): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('urlAccessToken'));
    }

    public function setUrlResourceOwnerDetailsAttribute(string $urlResourceOwnerDetails): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('urlResourceOwnerDetails'));
    }

    public function setAccessTokenMethodAttribute(string $accessTokenMethod): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('accessTokenMethod'));
    }

    public function setAccessTokenResourceOwnerIdAttribute(string $accessTokenResourceOwnerId): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('accessTokenResourceOwnerId'));
    }

    public function setScopeSeparatorAttribute(string $scopeSeparator): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('scopeSeparator'));
    }

    public function setResponseErrorAttribute(string $responseError): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('responseError'));
    }

    public function setResponseCodeAttribute(string $responseCode): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('responseCode'));
    }

    public function setResponseResourceOwnerIdAttribute(string $responseResourceOwnerId): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('responseResourceOwnerId'));
    }

    public function setScopesAttribute(string $scopes): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('scopes'));
    }

    public function setStateAttribute(string $state): void
    {
        $this->provider_options = array_merge($this->provider_options, compact('state'));
    }

    // Client attributes
    public function setBaseUriAttribute(string $base_uri): void
    {
        $this->client_options = array_merge($this->client_options, compact('base_uri'));
    }
}
