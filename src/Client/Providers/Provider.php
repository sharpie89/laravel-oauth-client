<?php

namespace Sharpie89\LaravelOAuthClient\Client\Providers;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Provider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    private string $driver;
    private string $urlAuthorize = 'login/oauth/authorize';
    private string $urlAccessToken = 'login/oauth/access_token';
    private string $urlResourceOwnerDetails = 'api/v1/user';
    private ?array $scopes = null;
    private string $responseResourceOwnerId = 'id';
    private string $responseError = 'error';

    /**
     * @var string
     */
    private $accessTokenResourceOwnerId;

    /**
     * @var string
     */
    private $accessTokenMethod;

    /**
     * @var string
     */
    private $scopeSeparator;

    /**
     * @var string
     */
    private $responseCode;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $possible = $this->getConfigurableOptions();
        $configured = array_intersect_key($options, array_flip($possible));

        foreach ($configured as $key => $value) {
            $this->$key = $value;
        }

        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);

        parent::__construct($options, $collaborators);
    }

    protected function getConfigurableOptions(): array
    {
        return [
            'driver',
            'urlAuthorize',
            'urlAccessToken',
            'urlResourceOwnerDetails',
            'accessTokenMethod',
            'accessTokenResourceOwnerId',
            'scopeSeparator',
            'responseError',
            'responseCode',
            'responseResourceOwnerId',
            'scopes',
        ];
    }

    public function getBaseAuthorizationUrl(): string
    {
        return config("oauth-drivers.{$this->driver}.urlAuthorize", $this->urlAuthorize);
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return config("oauth-drivers.{$this->driver}.urlAccessToken", $this->urlAccessToken);
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return config("oauth-drivers.{$this->driver}.urlResourceOwnerDetails", $this->urlResourceOwnerDetails);
    }

    public function getDefaultScopes(): ?array
    {
        return $this->scopes;
    }

    protected function getAccessTokenMethod(): string
    {
        return $this->accessTokenMethod ?: parent::getAccessTokenMethod();
    }

    protected function getAccessTokenResourceOwnerId(): ?string
    {
        return $this->accessTokenResourceOwnerId ?: parent::getAccessTokenResourceOwnerId();
    }

    protected function getScopeSeparator(): string
    {
        return $this->scopeSeparator ?: parent::getScopeSeparator();
    }

    /**
     * @inheritdoc
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (!empty($data[$this->responseError])) {
            $error = $data[$this->responseError];
            if (!is_string($error)) {
                $error = var_export($error, true);
            }
            $code  = $this->responseCode && !empty($data[$this->responseCode])? $data[$this->responseCode] : 0;
            if (!is_int($code)) {
                $code = intval($code);
            }
            throw new IdentityProviderException($error, $code, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new GenericResourceOwner($response, $this->responseResourceOwnerId);
    }

    /**
     * @inheritDoc
     */
    protected function appendQuery($url, $query): string
    {
        $baseUri = $this->getHttpClient()
            ->getConfig('base_uri');

        return parent::appendQuery("{$baseUri}/{$url}", $query);
    }
}
