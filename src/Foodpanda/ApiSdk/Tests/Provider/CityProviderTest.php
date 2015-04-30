<?php

namespace Foodpanda\ApiSdk\Tests\Provider;

use Foodpanda\ApiSdk\Api\LocationApiClient;
use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Foodpanda\ApiSdk\Tests\ApiSdkTestSuite;

class CityProviderTest extends ApiSdkTestSuite
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

    public function testFindWithValidId()
    {
        $locationApiClient = new LocationApiClient($this->getClient(), '', '');

        $serializer = ApiFactory::createSerializer();

        $cityProvider = new CityProvider(
            $locationApiClient,
            $serializer
        );
        
        static::assertInstanceOf(City::class, $cityProvider->find(5));
    }

    public function testFindWithInvalidId()
    {
        $this->setExpectedException('\GuzzleHttp\Exception\ClientException');

        $locationApiClient = new LocationApiClient($this->getClient(), '', '');

        $serializer = ApiFactory::createSerializer();

        $cityProvider = new CityProvider(
            $locationApiClient,
            $serializer
        );

        $cityProvider->find(99999);
    }
}
