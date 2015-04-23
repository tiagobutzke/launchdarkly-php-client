<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\AbstractApiClient;
use Foodpanda\ApiSdk\Serializer;

abstract class AbstractProvider
{
    /**
     * @var AbstractApiClient
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param AbstractApiClient $client
     * @param Serializer $serializer
     */
    public function __construct(AbstractApiClient $client, Serializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }
}
