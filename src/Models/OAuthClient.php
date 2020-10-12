<?php

namespace Sharpie89\LaravelOAuthClient\Models;

use Illuminate\Database\Eloquent\Model;
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
}
