<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Api\Auth\Credentials;

class CustomerApiClient extends AbstractApiClient
{
    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getCustomers(AccessToken $token, array $arguments = array())
    {
        $request = $this->client->createRequest('GET', 'customers', $arguments);

        $this->attachAuthenticationDataToRequest($request, $token);

        return $this->send($request)['data'];
    }

    /**
     * @param Credentials $credentials
     *
     * @return array
     */
    public function authenticate(Credentials $credentials)
    {
        $config = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => 'API_CUSTOMER',
            'username'      => $credentials->getUsername(),
            'password'      => $credentials->getPassword(),
            'grant_type'    => 'password',
        ];

        $request = $this->client->createRequest('POST', 'oauth2/token', array('body' => $config));

        return $this->send($request);
    }
}
