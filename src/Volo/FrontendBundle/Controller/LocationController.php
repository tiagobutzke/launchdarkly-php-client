<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByCity($city);

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

    /**
     * @Route(
     *      "/search/{lat}/{lng}",
     *      name="search_vendors",
     *      options={
     *          "expose"=true
 *          }
     * )
     * @Template()
     * @Method({"GET"})
     *
     * @param float $lat
     * @param float $lng
     *
     * @return array
     */
    public function searchAction($lat, $lng)
    {
        return [
            'vendors' => [],
        ];
    }
}
