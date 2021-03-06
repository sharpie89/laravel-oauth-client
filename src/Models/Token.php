<?php

namespace Sharpie89\LaravelOAuthClient\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sharpie89\LaravelOAuthClient\Casts\AccessTokenCast;

/**
 * @property AccessTokenInterface access_token
 * @property Client client
 * @property string state
 * @property string authorization_url
 * @property-write string code
 * @method Builder state(string $state)
 */
class Token extends Model
{
    public const GRANT_AUTHORIZATION_CODE = 'authorization_code';
    public const GRANT_REFRESH_TOKEN = 'refresh_token';

    protected $fillable = [
        'tokenizable_type',
        'tokenizable_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'access_token' => AccessTokenCast::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
     * @throws IdentityProviderException
     */
    public function setCodeAttribute(string $code): void
    {
        $this->access_token = $this->client->authenticate(self::GRANT_AUTHORIZATION_CODE, [
            'code' => $code
        ]);
    }

    /**
     * @return $this
     * @throws IdentityProviderException
     */
    public function refreshAccessToken(): self
    {
        $this->access_token = $this->client->authenticate(self::GRANT_REFRESH_TOKEN, [
            'refresh_token' => $this->access_token->getRefreshToken()
        ]);

        $this->save();

        return $this;
    }

    public function scopeState(Builder $query, string $state): Builder
    {
        return $query->where('state', $state);
    }

    public function tokenizable(): MorphTo
    {
        return $this->morphTo();
    }
}
