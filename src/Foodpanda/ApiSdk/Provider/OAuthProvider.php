<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\Auth\Credentials;
use Foodpanda\ApiSdk\Api\OAuthApiClient;

class OAuthProvider extends AbstractProvider
{
    /**
     * @var OAuthApiClient
     */
    protected $client;

    /**
     * @param Credentials $credentials
     *
     * @return array
     */
    public function authenticate(Credentials $credentials)
    {
        return $this->client->authenticate($credentials);
    }

    /**
     * @return array
     */
    public function authenticateClient()
    {
        return $this->client->authenticateClient();
    }
}
