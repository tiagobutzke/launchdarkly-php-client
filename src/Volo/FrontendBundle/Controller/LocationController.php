<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;

class LocationController extends Controller
{
    /**
     * @Route(
     *      "/city/{cityUrlKey}",
     *      name="volo_location_search_vendors_by_city",
     *      requirements={"cityUrlKey"="[a-z-]+"}
     * )
     * @Route(
     *      "/search/lat/{latitude}/lng/{longitude}/plz/{postcode}/city/{city}",
     *      name="volo_location_search_vendors_by_gps",
     *      options={"expose"=true},
     *      requirements={
     *          "latitude"="-?(\d*[.])?\d+",
     *          "longitude"="-?(\d*[.])?\d+",
     *          "postcode"="\d+"
     *      }
     * )
     * @Method({"GET"})
     * @Template("VoloFrontendBundle:Location:vendors_list.html.twig")
     * @ParamConverter("location", converter="user_location_converter")
     *
     * @param AbstractLocation $location
     *
     * @return array
     */
    public function locationAction(AbstractLocation $location)
    {
        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByLocation($location);

        list($openVendors, $closedVendorsWithPreorder) = $this->filterOpenClosedVendors($vendors->getItems());

        return [
            'openVendors'   => $openVendors,
            'closedVendors' => $closedVendorsWithPreorder
        ];
    }

    /**
     * @param VendorsCollection $items
     *
     * @return array
     */
    protected function filterOpenClosedVendors(VendorsCollection $items)
    {
        /** @var Vendor[] $openVendors */
        $openVendors = $items->filter(function (Vendor $vendor) {
            return $vendor->isIsPickupEnabled() || $vendor->isIsDeliveryEnabled();
        });

        /** @var Vendor[] $closedVendorsWithPreorder */
        $closedVendorsWithPreorder = $items->filter(function (Vendor $vendor) {
            return !$vendor->isIsPickupEnabled() && !$vendor->isIsDeliveryEnabled() && $vendor->isIsPreorderEnabled();
        });

        return [$openVendors, $closedVendorsWithPreorder];
    }
}
