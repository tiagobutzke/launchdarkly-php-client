<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Foodpanda\ApiSdk\Provider\VendorProvider;

class DeliverabilityService
{
    /**
     * @var VendorProvider
     */
    protected $vendorProvider;

    /**
     * @param VendorProvider $vendorProvider
     */
    public function __construct(VendorProvider $vendorProvider)
    {
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @param int $vendorId
     * @param double $latitude
     * @param double $longitude
     *
     * @return bool
     */
    public function isDeliverableLocation($vendorId, $latitude, $longitude)
    {
        $location = new GpsLocation($latitude, $longitude);

        try {
            return $this->vendorProvider->isDeliverable($vendorId, $location);
        } catch(ValidationEntityException $e) {
            return false;
        }
    }
}
