# laravel-oauth-client
An OAuth client for laravel using [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client). Install the package with composer:
```
composer require sharpie89/laravel-oauth-client
```

## Usage
Use the `oauth-drivers` config to add drivers that hold the default configuration for oauth endpoints:

```php
return [
    'gitlab' => [
        'urlAuthorize' => 'oauth/authorize',
        'urlAccessToken' => 'oauth/token',
        'urlResourceOwnerDetails' => 'api/v4/user'
    ]
];
```

Create clients by using the Client model:
```php
new Sharpie89\LaravelOauthClient\Models\Client([
    'driver' => 'gitlab',
    'provider_options' => [
        'client_id' => '<client_id>',
        'client_secret' => '<client_secret>',
    ],
    'client_options' => [
        'base_uri' => 'https://gitlab.com'
    ]
]);
```

Access tokens are in a seperate model that can be assigned to any model by the `Sharpie89\LaravelOAuthClient\Traits\Tokenizable` trait:
```php
class MyModel extends Illuminate\Database\Eloquent\Model {
  use \Sharpie89\LaravelOAuthClient\Traits\Tokenizable;
}
```

The Token model has the access_token column, which is a serialized `League\OAuth2\Client\Token\AccessToken` object. All the basic functionality such as getting an access token through code and refreshing the access token can be handled by the Token model.

Assign tokens to the Client by using your model:
```php
$token = $myModel->createTokenWith($client);
```

Or by using the client:
```php
$token = $client->createTokenFor($myModel);
```

The `Sharpie89\LaravelOauthClient\Models\Client` model generates a `Sharpie89\LaravelOAuthClient\Client\Providers\Provider` object, which is basically a `League\OAuth2\Client\Provider\GenericProvider` object, but when generating the authorizationUrl it uses the GuzzleHttp client's base_uri config.

Use the `Sharpie89\LaravelOauthClient\Http\Middleware\OAuthCallback` middleware on your callback route to automatically handle the authorization code. The callback route will receive the token within the request after the middleware has handled the request.

If you want to change the default driver options, just add a default driver to the `oauth-drivers` config:
```php
return [
    'default' => [
        'urlAccessToken' => 'oauth/somewhere/token',
        'redirectUri' => url('custom/callback/oauth')
    ]
];
```

The priority on which provider options is chosen is:
- 1: provider_options column in the Client model
- 2: driver_config from oauth-drivers config file (such as gitlab as the example)
- 3: the "default" config from oauth-drivers config file
- 4: $requiredOptions in the Client model

All provider options can be set in the driver config or the default config.
