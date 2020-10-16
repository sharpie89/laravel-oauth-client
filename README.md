# laravel-oauth-client
An OAuth client for laravel using [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client). Install the package with composer:
```
composer require sharpie89/laravel-oauth-client
```

## Usage
Use the `oauth-drivers` config to add drivers that hold the default configuration for oauth endpoints:

```php
'gitlab' => [
  'urlAuthorize' => 'oauth/authorize',
  'urlAccessToken' => 'oauth/token',
  'urlResourceOwnerDetails' => 'api/v4/user'
],
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

Assign tokens to the Client by using your model.

The `Sharpie89\LaravelOauthClient\Models\Client` model generates a `Sharpie89\LaravelOAuthClient\Client\Providers\Provider` object, which is basically a `League\OAuth2\Client\Provider\GenericProvider` object, but when generating the authorizationUrl it uses the GuzzleHttp client's base_uri config. 

Access tokens are in a seperate model that can be assigned to any model by the `Sharpie89\LaravelOAuthClient\Traits\Tokenizable` trait:
```php
class MyModel extends Model {
  use Tokenizable;
}
```

The Token model has the access_token column, which is a serialized `League\OAuth2\Client\Token\AccessToken` object. All the basic functionality such as getting an access token through code and refreshing the access token can be handled by the Token model.

