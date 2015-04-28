<?php

namespace Foodpanda\ApiSdk\Api;

class CmsApiClient extends AbstractApiClient
{
    /**
     * @return array
     */
    public function getCms()
    {
        $request = $this->client->createRequest('GET', 'cms?mobilePagesOnly=false');

        return $this->send($request)['data'];
    }
}
