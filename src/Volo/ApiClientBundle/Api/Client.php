<?php

namespace Volo\ApiClientBundle\Api;

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
     * @todo: Request and JSON parsing error handling
     *
     * @param array $arguments
     *
     * @return array
     */
    public function getCities(array $arguments = array())
    {
        return $this->client->get(sprintf('/api/v3/cities?%s', http_build_query($arguments)))->json();
    }
}
