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
     * @param string $include
     *
     * @return array
     */
    public function getVendor(
        $id,
        $include = 'cuisines,food_characteristics,menus,menu_categories,products,product_variations,discounts,metadata'
    ) {
        $request = $this->client->createRequest(
            'GET',
            [
                'vendors/{vendor_id}?include={include}',
                [
                    'vendor_id' => $id,
                    'include' => $include
                ],
            ]
        );

        return $this->send($request)['data'];
    }
}
