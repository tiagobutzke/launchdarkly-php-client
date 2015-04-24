<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\RequestInterface;

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
     * @param RequestInterface $request
     *
     * @return array
     *
     * @throw ClientException|ParseException
     */
    protected function send(RequestInterface $request)
    {
        try {
            $response = $this->client->send($request);

            return $response->json();
        } catch (ParseException $e) {
            // @todo
            throw $e;
        } catch (ClientException $exception) {
            $this->formatErrorMessage($exception->getResponse()->json(), $exception);
        }

        throw new \RuntimeException('You should not reach this point.');
    }

    /**
     * @param RequestInterface $request
     * @param AccessToken $accessToken
     */
    protected function attachAuthenticationDataToRequest(RequestInterface $request, AccessToken $accessToken)
    {
        $oauth2 = new Oauth2Subscriber();
        $oauth2->setAccessToken($accessToken);
        $refreshToken = $accessToken->getRefreshToken();
        if (null !== $refreshToken) {
            $oauth2->setRefreshToken($refreshToken);
        }

        $request->getConfig()->set('auth', 'oauth2');
        $request->getEmitter()->attach($oauth2);
    }

    /**
     * @param array           $body
     * @param ClientException $exception
     *
     * @throws ClientException
     */
    protected function formatErrorMessage(array $body, ClientException $exception)
    {
        if (array_key_exists('error', $body) && array_key_exists('error_description', $body)) {
            $errorMessage = sprintf('%s: %s', $body['error'], $body['error_description']);

            throw new ClientException($errorMessage, $exception->getRequest(), $exception->getResponse());
        }

        if (array_key_exists('data', $body)
            && array_key_exists('exception_type', $body['data'])
            && array_key_exists('message', $body['data'])
        ) {
            $errorMessage = sprintf('%s: %s', $body['data']['exception_type'], $body['data']['message']);

            throw new ClientException($errorMessage, $exception->getRequest(), $exception->getResponse());
        }

        if (array_key_exists('data', $body)
            && array_key_exists('items', $body['data'])
            && $exception->getResponse()
            && $exception->getResponse()->getStatusCode() === 400
        ) {
            $errorMessage = json_encode(json_decode($exception->getResponse()->getBody()), JSON_PRETTY_PRINT);

            throw new ClientException($errorMessage, $exception->getRequest(), $exception->getResponse());
        }

        throw $exception;
    }

    /**
     * @return array
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

        return $this->send($request);
    }
}
