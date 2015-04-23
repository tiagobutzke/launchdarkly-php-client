<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\VendorApiClient;
use Foodpanda\ApiSdk\Entity\Geocoding\Area;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorResults;

class VendorProvider extends AbstractProvider
{
    /**
     * @var VendorApiClient
     */
    protected $client;

    /**
     * @param Area $area
     *
     * @return VendorResults
     */
    public function findVendorsByArea(Area $area)
    {
        return $this->serializer->denormalize($this->client->getVendorsByArea($area->getId()), VendorResults::class);
    }

    /**
     * @return Vendor
     */
    public function find($id)
    {
        return $this->serializer->denormalize($this->client->getVendor($id), Vendor::class);
    }
}
