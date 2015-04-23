<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LocationController extends Controller
{
    /**
     * @Route("/city/{id}", name="city")
     * @Template()
     */
    public function cityAction($id)
    {
        $city    = $this->get('volo_frontend.provider.city')->find($id);
        $areas   = $this->get('volo_frontend.provider.area')->findByCity($id);
        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByArea($areas->getItems()->first());

        /** @var Vendor[] $openVendors */
        $openVendors = $vendors->getItems()->filter(function (Vendor $vendor) {
            return $vendor->isIsPickupEnabled() || $vendor->isIsDeliveryEnabled();
        });

        /** @var Vendor[] $closedVendorsWithPreorder */
        $closedVendorsWithPreorder = $vendors->getItems()->filter(function (Vendor $vendor) {
            return !$vendor->isIsPickupEnabled() && !$vendor->isIsDeliveryEnabled() && $vendor->isIsPreorderEnabled();
        });

        return [
            'city'          => $city,
            'openVendors'   => $openVendors,
            'closedVendors' => $closedVendorsWithPreorder
        ];
    }
}
