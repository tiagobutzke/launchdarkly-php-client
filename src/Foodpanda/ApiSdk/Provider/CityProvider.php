<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\LocationApiClient;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\City\CityResults;

class CityProvider extends AbstractProvider
{
    /**
     * @var LocationApiClient
     */
    protected $client;

    /**
     * @param int $id
     *
     * @return City
     */
    public function find($id)
    {
        return $this->serializer->denormalizeCity($this->client->getCity($id));
    }

    /**
     * @return CityResults
     */
    public function findAll()
    {
        return $this->serializer->denormalizeCities($this->client->getCities());
    }
}
