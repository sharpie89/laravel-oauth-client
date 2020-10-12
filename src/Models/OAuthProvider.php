<?php

namespace Sharpie89\LaravelOAuthClient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Casts\AccessTokenCast;

/**
 * @property AccessTokenInterface $access_token
 * @property OAuthClient client
 * @property string state
 */
class OAuthProvider extends Model
{
    public const GRANT_AUTHORIZATION_CODE = 'authorization_code';
    public const GRANT_REFRESH_TOKEN = 'refresh_token';

    /**
     * @var string[]
     */
    protected $fillable = [
        'state'
    ];
    /**
     * @var string[]
     */
    protected $casts = [
        'access_token' => AccessTokenCast::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(OAuthClient::class);
    }

    public function getAuthorizationUrlAttribute(): string
    {
        $authorizationUrl = $this->client->provider->getAuthorizationUrl();

        $this->state = $this->client->provider->getState();
        $this->save();

        return $authorizationUrl;
    }

    /**
     * @param  string  $code
     * @return $this
     * @throws IdentityProviderException
     */
    public function authenticateByCode(string $code): self
    {
        $this->access_token = $this->client->provider->getAccessToken(self::GRANT_AUTHORIZATION_CODE, [
            'code' => $code
        ]);

        $this->save();

        return $this;
    }

    /**
     * @return $this
     * @throws IdentityProviderException
     */
    public function authenticateByRefreshToken(): self
    {
        $this->access_token = $this->client->provider->getAccessToken(self::GRANT_REFRESH_TOKEN, [
            'refresh_token' => $this->access_token->getRefreshToken()
        ]);

        $this->save();

        return $this;
    }

    public function getRequest($method, $url, array $options = [])
}
