<?php

namespace Foodpanda\ApiSdk\Api;

class LocationApiClient extends AbstractApiClient
{
    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getCities(array $arguments = array())
    {
        $request = $this->client->createRequest('GET', 'cities', $arguments);

        return $this->send($request);
    }
}
