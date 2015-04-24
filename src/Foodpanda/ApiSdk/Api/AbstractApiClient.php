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
    }

    /**
     * @param RequestInterface $request
     * @param AccessToken $accessToken
     */
    protected function attachAuthenticationDataToRequest(RequestInterface $request, AccessToken $accessToken)
    {
        $oauth2 = new Oauth2Subscriber();
        $oauth2->setAccessToken($accessToken);
        $oauth2->setRefreshToken($accessToken->getRefreshToken());

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

        throw $exception;
    }
}
