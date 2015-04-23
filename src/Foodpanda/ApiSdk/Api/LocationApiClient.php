<?php

namespace Foodpanda\ApiSdk\Api;

class LocationApiClient extends AbstractApiClient
{
    /**
     * @return array
     */
    public function getCities()
    {
        $request = $this->client->createRequest('GET', 'cities');

        return $this->send($request)['data'];
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getCity($id)
    {
        $request = $this->client->createRequest('GET', [
            'cities/{city_id}',
            ['city_id' => $id]
        ]);

        return $this->send($request)['data'];
    }

    /**
     * @param int $id
     */
    public function getAreasByCity($id)
    {
        $request = $this->client->createRequest('GET', [
            'areas/geocoding',
            ['query' => ['city_id' => $id]]
        ]);

        return $this->send($request)['data'];
    }
}
