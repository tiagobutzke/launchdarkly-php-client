<?php

namespace Volo\ApiClientBundle\Api;

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
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
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
}
