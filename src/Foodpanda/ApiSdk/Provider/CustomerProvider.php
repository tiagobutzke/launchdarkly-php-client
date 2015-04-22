<?php

namespace Foodpanda\ApiSdk\Provider;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Api\CustomerApiClient;

class CustomerProvider extends AbstractProvider
{
    /**
     * @var CustomerApiClient
     */
    protected $client;

    /**
     * @param AccessToken $token
     *
     * @return array
     */
    public function getCustomer(AccessToken $token)
    {
        return $this->client->getCustomers($token, true);
    }

    /**
     * @param AccessToken $token
     *
     * @return array
     */
    public function getCustomerWithoutAddresses(AccessToken $token)
    {
        return $this->client->getCustomers($token, false);
    }

}
