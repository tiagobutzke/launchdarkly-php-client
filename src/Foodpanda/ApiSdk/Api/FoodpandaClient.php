<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\ApiException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Message\RequestInterface;

class FoodpandaClient extends Client
{
    /**
     * @param RequestInterface $request
     *
     * @return array
     *
     * @throw ApiException
     * @throw ParseException
     */
    public function send(RequestInterface $request)
    {
        try {
            $response = parent::send($request);

            return $response->json();
        } catch (ParseException $e) {
            // @todo
            throw $e;
        } catch (ClientException $exception) {
            $this->formatErrorMessage($exception->getResponse()->json(), $exception);
        }

        throw new \LogicException('You should not reach this point.');
    }

    /**
     * @param RequestInterface $request
     * @param AccessToken $accessToken
     */
    public function attachAuthenticationDataToRequest(RequestInterface $request, AccessToken $accessToken)
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
     * @throws ApiErrorException
     * @throws ApiException
     * @throws ValidationEntityException
     */
    protected function formatErrorMessage(array $body, ClientException $exception)
    {
        if (array_key_exists('error', $body) && array_key_exists('error_description', $body)) {
            $errorMessage = sprintf('%s: %s', $body['error'], $body['error_description']);

            throw new ApiErrorException($errorMessage, $exception->getRequest(), $exception->getResponse());
        }

        if (array_key_exists('data', $body)
            && array_key_exists('exception_type', $body['data'])
            && array_key_exists('message', $body['data'])
        ) {
            $errorMessage = sprintf('%s: %s', $body['data']['exception_type'], $body['data']['message']);

            throw new ApiException($errorMessage, $exception->getRequest(), $exception->getResponse());
        }

        if (array_key_exists('data', $body)
            && array_key_exists('items', $body['data'])
            && $exception->getResponse()
            && $exception->getResponse()->getStatusCode() === 400
        ) {
            $errorMessage = json_encode(json_decode($exception->getResponse()->getBody()), JSON_PRETTY_PRINT);

            throw new ValidationEntityException($errorMessage, $exception->getRequest(), $exception->getResponse());
        }

        throw $exception;
    }
}
