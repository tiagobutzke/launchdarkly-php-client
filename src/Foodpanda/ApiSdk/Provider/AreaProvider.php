<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\LocationApiClient;
use Foodpanda\ApiSdk\Entity\Geocoding\AreaResults;

class AreaProvider extends AbstractProvider
{
    /**
     * @var LocationApiClient
     */
    protected $client;

    /**
     * @param int $id
     *
     * @return AreaResults
     */
    public function findByCity($id)
    {
        return $this->serializer->denormalize($this->client->getAreasByCity($id), AreaResults::class);
    }
}
