<?php

namespace Sharpie89\LaravelOAuthClient\Client\Providers;

use InvalidArgumentException;
use League\OAuth2\Client\Provider\GenericProvider;

class GenericOAuthProvider extends GenericProvider
{
    private string $driver;
    private string $url;

    private array $defaultRequiredOptions = [
        'urlAuthorize' => '/login/oauth/authorize',
        'urlAccessToken' => '/login/oauth/access_token',
        'urlResourceOwnerDetails' => '/api/v1/user'
    ];

    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->assertDriverOptions($options);

        $possible = $this->getDriverOptions();
        $configured = array_intersect_key($options, array_flip($possible));

        foreach ($configured as $key => $value) {
            $this->$key = $value;
        }

        $this->setRequiredOptions($options);

        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);

        $options['redirectUri'] = $this->getRedirectUri();

        parent::__construct($options, $collaborators);
    }

    protected function getDriverOptions(): array
    {
        return [
            'driver',
            'url',
        ];
    }

    protected function createRequest($method, $url, $token, array $options)
    {
        return parent::createRequest($method, $this->url . $url, $token, $options);
    }

    protected function getRedirectUri(): string
    {
        return url('oauth/callback');
    }

    private function setRequiredOptions(array &$options): void
    {
        foreach ($this->defaultRequiredOptions as $key => $value) {
            $options[$key] = $this->url.config("oauth-drivers.{$this->driver}.{$key}", $value);
        }
    }

    private function assertDriverOptions(array $options): void
    {
        $missing = array_diff_key(array_flip($this->getDriverOptions()), $options);

        if (!empty($missing)) {
            throw new InvalidArgumentException(
                'Driver options not defined: '.implode(', ', array_keys($missing))
            );
        }
    }
}
