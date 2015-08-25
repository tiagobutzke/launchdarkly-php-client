<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends Controller
{
    /**
     * @Route(
     *      "/city/{cityUrlKey}",
     *      name="volo_location_search_vendors_by_city",
     *      requirements={"cityUrlKey"="[a-zA-Z-]+"}
     * )
     * @Route(
     *      "/restaurants/lat/{latitude}/lng/{longitude}/plz/{postcode}/city/{city}/address/{address}/{street}/{building}",
     *      name="volo_location_search_vendors_by_gps",
     *      options={"expose"=true},
     *      defaults={"street"="", "building"=""},
     *      requirements={
     *          "latitude"="-?(\d*[.])?\d+",
     *          "longitude"="-?(\d*[.])?\d+",
     *          "postcode"="[^/]+"
     *      }
     * )
     * @Method({"GET"})
     * @Template("VoloFrontendBundle:Location:vendors_list.html.twig")
     * @ParamConverter("location", converter="user_location_converter")
     *
     * @param Request $request
     * @param AbstractLocation $location
     * @param array $formattedLocation
     *
     * @return array
     */
    public function locationAction(Request $request, AbstractLocation $location, array $formattedLocation)
    {
        /** @var City $city */
        $city = $request->attributes->get('cityObj');

        if ($location instanceof CityLocation && $request->attributes->get('cityUrlKey') !== $city->getUrlKey()) {
            return $this->redirectToRoute('volo_location_search_vendors_by_city', ['cityUrlKey' => $city->getUrlKey()]);
        }

        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByLocation($location);

        /** @var $openVendors VendorsCollection */
        /** @var $closedVendorsWithPreorder VendorsCollection */
        list($openVendors, $closedVendorsWithPreorder) = $vendors->getItems()
            ->filter(function (Vendor $vendor) { // filter restaurant closed
                return !$vendor->getSchedules()->isEmpty();
            })->partition(function ($key, Vendor $vendor) {
                return $this->get('volo_frontend.service.schedule')->isVendorOpen($vendor, new \DateTime());
            });

        return [
            'gpsSearch' => $location->getLocationType() === 'polygon',
            'formattedLocation' => $formattedLocation,
            'vendors' => $vendors->getItems(),
            'openVendors' => $openVendors,
            'closedVendors' => $closedVendorsWithPreorder,
            'city' => $city,
            'hasQueryParams' => $request->query->count() > 0,
            'location' => $this->get('volo_frontend.service.customer_location')->get($request->getSession())
        ];
    }
}
