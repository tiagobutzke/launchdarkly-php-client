<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Api\Auth\Credentials;

class Authenticator
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var FoodpandaClient
     */
    protected $client;

    /**
     * @param FoodpandaClient $client
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(FoodpandaClient $client, $clientId, $clientSecret)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
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

        $request = $this->client->createRequest('POST', 'oauth2/token', array('body' => $config));

        $data = $this->client->send($request);

        return new AccessToken($data['access_token'], $data['token_type'], $data);
    }

    /**
     * @return AccessToken
     */
    public function authenticateClient()
    {
        $config = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => 'API_CUSTOMER',
            'grant_type'    => 'client_credentials',
        ];

        $request = $this->client->createRequest('POST', 'oauth2/token', array('body' => $config));

        $data =  $this->client->send($request);

        return new AccessToken($data['access_token'], $data['token_type'], $data);
    }
}
