<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Entity\Geocoding\AreaResults;

class AreaProvider extends AbstractProvider
{
    /**
     * @param int $id
     *
     * @return AreaResults
     */
    public function findByCity($id)
    {
        $request = $this->client->createRequest(
            'GET',
            [
                'areas/geocoding',
                ['query' => ['city_id' => $id]],
            ]
        );

        $data = $this->client->send($request)['data'];

        return $this->serializer->denormalizeGeocodingAreas($data);
    }
}
