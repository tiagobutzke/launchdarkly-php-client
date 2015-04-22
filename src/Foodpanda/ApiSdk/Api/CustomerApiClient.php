<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Api\Auth\Credentials;

class CustomerApiClient extends AbstractApiClient
{
    /**
     * @param AccessToken $token
     * @param bool $withAddress
     *
     * @return array
     */
    public function getCustomers(AccessToken $token, $withAddress)
    {
        // TODO: add "include: $withAddress" parameter in the request
        $request = $this->client->createRequest('GET', 'customers');

        $this->attachAuthenticationDataToRequest($request, $token);

        return $this->send($request)['data'];
    }
}
