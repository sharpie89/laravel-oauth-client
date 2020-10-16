<?php

namespace Sharpie89\LaravelOAuthClient\Client\Providers;

use League\OAuth2\Client\Provider\GenericProvider;

class Provider extends GenericProvider
{
    /**
     * @inheritDoc
     */
    protected function appendQuery($url, $query): string
    {
        return parent::appendQuery("{$this->getHttpClient()->getConfig('base_uri')}/{$url}", $query);
    }
}
