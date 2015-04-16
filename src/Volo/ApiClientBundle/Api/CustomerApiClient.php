<?php

namespace Volo\ApiClientBundle\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Volo\ApiClientBundle\Api\Auth\Credentials;
use CommerceGuys\Guzzle\Oauth2\GrantType\PasswordCredentials;

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
        
        return $this->send($request);
    }

    /**
     * @param Credentials $credentials
     *
     * @return AccessToken
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

        $refreshToken = new PasswordCredentials($this->client, $config);

        return $refreshToken->getToken();
    }
}
