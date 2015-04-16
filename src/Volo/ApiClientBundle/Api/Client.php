<?php

namespace Volo\ApiClientBundle\Api;

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;

class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
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
     * @param array $arguments
     *
     * @return array
     */
    public function getCities(array $arguments = array())
    {
        $request = $this->client->createRequest('GET', '/api/v4/cities', $arguments);

        return $this->send($request);
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getTranslations(array $arguments = array())
    {
        $request = $this->client->createRequest('GET', '/api/v4/translations', $arguments);

        return $this->send($request);
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getCustomers(array $arguments = array())
    {
        $request = $this->client->createRequest('GET', '/api/v4/customers', $arguments);

        return $this->send($request);
    }
}
