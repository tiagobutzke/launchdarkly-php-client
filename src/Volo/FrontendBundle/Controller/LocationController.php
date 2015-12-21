<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\Cart\LocationInterface;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Volo\FrontendBundle\Exception\CityNotFoundException;
use Volo\FrontendBundle\Service\CustomerLocationService;

class LocationController extends BaseController
{
    /**
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
     * @param GpsLocation $location
     *
     * @return array
     */
    public function locationAction(Request $request, GpsLocation $location)
    {
        $gpsLocation = $this->saveCustomerLocation($request);
        $formattedLocation = $this->createFormattedLocation($gpsLocation, $location);

        $city = null;
        try {
            $city = $this->getCityService()->findCityByGpsLocation($location);
        } catch (CityNotFoundException $e) {
            // do nothing
        }

        $vendors = $this->getVendorService()->findAll($location);

        return $this->prepareViewData($request, $location, $formattedLocation, $vendors, $city);
    }

    /**
     * @Route(
     *      "/city/{cityUrlKey}",
     *      name="volo_location_search_vendors_by_city",
     *      requirements={"cityUrlKey"="[a-zA-Z-]+"}
     * )
     * @Method({"GET"})
     * @Template("VoloFrontendBundle:Location:vendors_list.html.twig")
     * @ParamConverter("location", converter="user_location_converter")
     * @ParamConverter("city", converter="city_location_converter")
     *
     * @param Request $request
     * @param CityLocation $location
     * @param City $city
     *
     * @return array
     */
    public function cityAction(Request $request, CityLocation $location, City $city)
    {
        $formattedLocation = $this->getCustomerLocationService()->createEmpty();

        if ($request->attributes->get('cityUrlKey') !== $city->getUrlKey()) {
            return $this->redirectToRoute('volo_location_search_vendors_by_city', ['cityUrlKey' => $city->getUrlKey()]);
        }

        $vendors = $this->getVendorService()->findAll($location);

        return $this->prepareViewData($request, $location, $formattedLocation, $vendors, $city);
    }

    /**
     * @param VendorsCollection $vendors
     *
     * @return array
     */
    private function createFilters(VendorsCollection $vendors)
    {
        $filters = [
            'cuisine' => [],
            'food_characteristics' => [],
        ];

        /** @var Vendor $item */
        foreach ($vendors as $item) {
            foreach ($item->getCuisines() as $cuisine) {
                $filters['cuisine'][$cuisine->getId()] = $cuisine;
            }

            foreach ($item->getFoodCharacteristics() as $characteristics) {
                $filters['food_characteristics'][$characteristics->getId()] = $characteristics;
            }
        }

        return $filters;
    }

    /**
     * @param array $gpsLocation
     * @param AbstractLocation $location
     *
     * @return array
     */
    private function createFormattedLocation(array $gpsLocation, AbstractLocation $location)
    {
        // this is to handle the case when the user select district / main area without a street address
        $deliveryAddress = $this->getCustomerLocationService()->format($gpsLocation);

        return [
            'type'             => $location->getLocationType(),
            'city'             => urldecode($gpsLocation[CustomerLocationService::KEY_CITY]),
            'postcode'         => $gpsLocation[CustomerLocationService::KEY_PLZ],
            'address'          => urldecode($gpsLocation[CustomerLocationService::KEY_ADDRESS]),
            'street'           => urldecode($gpsLocation[CustomerLocationService::KEY_STREET]),
            'building'         => urldecode($gpsLocation[CustomerLocationService::KEY_BUILDING]),
            'delivery_address' => urldecode($deliveryAddress)
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    private function saveCustomerLocation(Request $request)
    {
        $gpsLocation = $this->getCustomerLocationService()->create(
            $request->get(CustomerLocationService::KEY_LAT),
            $request->get(CustomerLocationService::KEY_LNG),
            $request->get(CustomerLocationService::KEY_PLZ),
            urldecode($request->get(CustomerLocationService::KEY_CITY)),
            urldecode($request->get(CustomerLocationService::KEY_ADDRESS)),
            urldecode($request->get(CustomerLocationService::KEY_STREET)),
            urldecode($request->get(CustomerLocationService::KEY_BUILDING))
        );

        $this->getCustomerLocationService()->set($request->getSession(), $gpsLocation);

        return $gpsLocation;
    }

    /**
     * @param Request $request
     * @param LocationInterface $location
     * @param array $formattedLocation
     * @param VendorsCollection $vendors
     * @param City $city
     * @param array $cityLocation
     *
     * @return array
     */
    private function prepareViewData(
        Request $request,
        LocationInterface $location,
        array $formattedLocation,
        VendorsCollection $vendors,
        $city,
        array $cityLocation = []
    ) {
        return [
            'hasQueryParams' => $request->query->count() > 0,
            'gpsSearch' => $location->getLocationType() === 'polygon',
            'formattedLocation' => $formattedLocation,
            'vendors' => $vendors,
            'city' => $city,
            'location' => $this->getCustomerLocationService()->get($request->getSession()),
            'cityLocation' => $cityLocation,
            'filters' => $this->createFilters($vendors)
        ];
    }
}
