<?php

namespace Foodpanda\ApiSdk\Tests\Provider;

use Foodpanda\ApiSdk\Api\LocationApiClient;
use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\EntityManager;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Foodpanda\ApiSdk\Tests\MockHandlerCallable;
use GuzzleHttp\Client;
use GuzzleHttp\Ring\Client\MockHandler;
use Foodpanda\ApiSdk\Entity\City\CityCollection;

class CityProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindAll()
    {
        $locationApiClient = new LocationApiClient($this->getClient(), '', '');

        $serializer = ApiFactory::createSerializer();

        $cityProvider = new CityProvider(
            $locationApiClient,
            $serializer
        );

        $cities = $cityProvider->findAll();

        $normalized = $serializer->normalize($cities);
        foreach ($normalized['items'] as &$object) {
            unset($object['main_area']);
        }

        static::assertEquals($locationApiClient->getCities(), $normalized);
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        $mock = new MockHandler(new MockHandlerCallable());

        return new Client(['handler' => $mock]);
    }
}
