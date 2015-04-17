<?php

namespace Volo\ApiClientBundle\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Client as GuzzleClient;

abstract class AbstractApiClient
{
    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @param GuzzleClient $client
     * @param string       $clientId
     * @param string       $clientSecret
     */
    public function __construct(GuzzleClient $client, $clientId, $clientSecret)
    {
        $this->client       = $client;
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throw RequestException|ParseException
     */
    protected function send(Request $request)
    {
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            // @todo
            throw $e;
        }

        try {
            return $response->json()['data'];
        } catch (ParseException $e) {
            // @todo
            throw $e;
        }
    }

    /**
     * @param Request     $request
     * @param AccessToken $accessToken
     */
    protected function attachAuthenticationDataToRequest(Request $request, AccessToken $accessToken)
    {
        $oauth2       = new Oauth2Subscriber();
        $oauth2->setAccessToken($accessToken);
        $oauth2->setRefreshToken($accessToken->getRefreshToken());
        
        $request->getConfig()->set('auth', 'oauth2');
        $request->getEmitter()->attach($oauth2);
    }
}
