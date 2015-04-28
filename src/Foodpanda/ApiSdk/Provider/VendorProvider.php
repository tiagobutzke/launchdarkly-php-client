<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Entity\Geocoding\Area;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorResults;

class VendorProvider extends AbstractProvider
{
    /**
     * @param Area $area
     *
     * @return VendorResults
     */
    public function findVendorsByArea(Area $area)
    {
        $request = $this->client->createRequest(
            'GET',
            'vendors',
            [
                'query' => [
                    'area_id' => $area->getId(),
                    'include' => 'cuisines'
                ]
            ]
        );


        $data = $this->client->send($request)['data'];

        return $this->serializer->denormalizeVendors($data);
    }

    /**
     * @param int $id
     *
     * @return Vendor
     */
    public function find($id)
    {
        $include = 'cuisines,food_characteristics,menus,menu_categories,products,product_variations,discounts,metadata';
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

        $data = $this->client->send($request)['data'];

        return $this->serializer->denormalizeVendor($data);
    }
}
