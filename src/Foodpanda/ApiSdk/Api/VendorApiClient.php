<?php

namespace Foodpanda\ApiSdk\Api;

class VendorApiClient extends AbstractApiClient
{
    /**
     * @param int $id
     *
     * @return array
     */
    public function getVendorsByArea($id)
    {
        $request = $this->client->createRequest(
            'GET',
            'vendors',
            [
                'query' => [
                    'area_id' => $id,
                    'include' => 'cuisines'
                ]
            ]
        );

        return $this->send($request)['data'];
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getVendor($id)
    {
        $request = $this->client->createRequest(
            'GET',
            [
                'vendors/{vendor_id}',
                [
                    'vendor_id' => $id
                ],
            ],
            [
                'query' => [
                    'include' => 'products,product_variations'
                ]
            ]
        );

        return $this->send($request)['data'];
    }
}
