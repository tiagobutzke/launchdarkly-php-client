<?php

namespace Foodpanda\ApiSdk\Api;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Api\Auth\Credentials;

class VendorApiClient extends AbstractApiClient
{
    /**
     * @return array
     */
    public function getVendorsByArea($id)
    {
        $request = $this->client->createRequest('GET', 'vendors', [
            'query' => [
                'area_id' => $id,
                'include' => 'cuisines'
            ]
        ]);

        return $this->send($request)['data'];
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getVendor($id)
    {
        $request = $this->client->createRequest('GET', [
            'vendors/{vendor_id}',
            ['vendor_id' => $id]
        ]);

        return $this->send($request)['data'];
    }
}
