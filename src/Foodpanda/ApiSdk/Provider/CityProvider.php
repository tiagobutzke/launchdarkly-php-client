<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\City\CityResults;

class CityProvider extends AbstractProvider
{
    /**
     * @param int $id
     *
     * @return City
     */
    public function find($id)
    {
        $request = $this->client->createRequest(
            'GET',
            [
                'cities/{city_id}',
                ['city_id' => $id]
            ]
        );

        $data = $this->client->send($request)['data'];

        return $this->serializer->denormalizeCity($data);
    }

    /**
     * @return CityResults
     */
    public function findAll()
    {
        $request = $this->client->createRequest('GET', 'cities');

        $data = $this->client->send($request)['data'];

        return $this->serializer->denormalizeCities($data);
    }
}
