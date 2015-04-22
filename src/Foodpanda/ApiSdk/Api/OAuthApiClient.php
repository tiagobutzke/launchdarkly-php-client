<?php

namespace Foodpanda\ApiSdk\Api;

use Foodpanda\ApiSdk\Api\Auth\Credentials;

class OAuthApiClient extends AbstractApiClient
{
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
