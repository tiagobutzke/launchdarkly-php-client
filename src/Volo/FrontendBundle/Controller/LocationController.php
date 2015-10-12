<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorResults;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Service\CustomerLocationService;

class LocationController extends BaseController
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
     *
     * @return array
     */
    public function locationAction(Request $request, AbstractLocation $location)
    {
        $city = $request->get('cityObject');
        $formattedLocation = $this->getCustomerLocationService()->createEmpty();

        if ($location instanceof CityLocation && $request->attributes->get('cityUrlKey') !== $city->getUrlKey()) {
            return $this->redirectToRoute('volo_location_search_vendors_by_city', ['cityUrlKey' => $city->getUrlKey()]);
        }

        if ($location instanceof GpsLocation) {
            $gpsLocation = $this->saveCustomerLocation($request);
            $formattedLocation = $this->createFormattedLocation($gpsLocation, $location);

            try {
                $city = $this->findCityByLocation($location);
            } catch (NotFoundHttpException $e) {
                // do nothing
            }
        }

        $vendorsGroups = $this->getVendorService()->findOpenClosedVendors($location);
        /** @var VendorResults $allVendors */
        list($openVendors, $closedVendorsWithPreorder, $allVendors) = $vendorsGroups;

        return [
            'hasQueryParams' => $request->query->count() > 0,
            'gpsSearch' => $location->getLocationType() === 'polygon',
            'formattedLocation' => $formattedLocation,
            'vendors' => $allVendors,
            'openVendors' => $openVendors,
            'closedVendors' => $closedVendorsWithPreorder,
            'city' => $city,
            'location' => $this->get('volo_frontend.service.customer_location')->get($request->getSession()),
            'filters' => $this->createFilters($allVendors)
        ];
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
     * @param GpsLocation $location
     *
     * @return City
     */
    private function findCityByLocation(GpsLocation $location)
    {
        try {
            $cities = $this->get('volo_frontend.provider.city')->findCitiesByLocation($location);
        } catch (ApiErrorException $e) {
            throw new NotFoundHttpException(
                sprintf('No cities found with coordinates : %f/%f', $location->getLatitude(), $location->getLongitude())
            );
        }

        if ($cities->getAvailableCount() === 0) {
            throw new NotFoundHttpException(
                sprintf('No cities found with coordinates : %f/%f', $location->getLatitude(), $location->getLongitude())
            );
        }

        return $cities->getItems()->first();
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
        $deliveryAddress = trim(
            sprintf(
                '%s %s, %s',
                urldecode($gpsLocation[CustomerLocationService::KEY_BUILDING]),
                urldecode($gpsLocation[CustomerLocationService::KEY_STREET]),
                urldecode($gpsLocation[CustomerLocationService::KEY_PLZ])
            )
        );
        $deliveryAddress = strpos($deliveryAddress, ',') === 0 ? substr($deliveryAddress, 1) : $deliveryAddress;

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
     * @return CustomerLocationService
     */
    private function getCustomerLocationService()
    {
        return $this->get('volo_frontend.service.customer_location');
    }
}
