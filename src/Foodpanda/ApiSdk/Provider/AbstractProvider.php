<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\Authenticator;
use Foodpanda\ApiSdk\Api\FoodpandaClient;
use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\Serializer;

abstract class AbstractProvider
{
    /**
     * @var FoodpandaClient
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @param FoodpandaClient $client
     * @param Serializer $serializer
     * @param Authenticator $authenticator
     */
    public function __construct(FoodpandaClient $client, Serializer $serializer, Authenticator $authenticator)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->authenticator = $authenticator;
    }

    /**
     * @return static
     */
    public static function createInstance()
    {
        $client = ApiFactory::createApiClient();
        $serializer = ApiFactory::createSerializer();
        $authenticator = new Authenticator($client, '', '');

        return new static($client, $serializer, $authenticator);
    }
}
