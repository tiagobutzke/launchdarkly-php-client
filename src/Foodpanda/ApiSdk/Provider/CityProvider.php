<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\LocationApiClient;
use Foodpanda\ApiSdk\EntityManager;
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
     * @return CityResults
     */
    public function find($id)
    {
        return $this->serializer->denormalize($this->client->getCity($id), CityResults::class);
    }

    /**
     * @return CityResults
     */
    public function findAll()
    {
        return $this->serializer->denormalize($this->client->getCities(), CityResults::class);
    }
}
