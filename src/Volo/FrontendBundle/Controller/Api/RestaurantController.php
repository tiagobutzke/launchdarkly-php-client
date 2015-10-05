<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Controller\BaseController;

/**
 * @Route("/api/v1/restaurants", defaults={"_format": "json"})
 */
class RestaurantController extends BaseController
{
    /**
     * @Route("/", name="api_restaurants_list", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function listAction(Request $request)
    {
        $location = new CityLocation(7);

        $cuisines = $request->query->get('cuisine');
        $food_characteristics = $request->query->get('food_characteristic');
        $includes = $request->query->get('includes', ['cuisines', 'food_characteristics']);

        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByLocation($location, $includes, $cuisines, $food_characteristics);
        $serializer = $this->getSerializer();

        return new JsonResponse($serializer->normalize($vendors));
    }

    /**
     * @param string $cityUrlKey
     *
     * @return City
     */
    protected function findCityByCode($cityUrlKey)
    {
        $filtered = $this->get('volo_frontend.provider.city')->findAll()->getItems()->filter(function($element) use ($cityUrlKey) {
            /** @var City $element */
            return strtolower($element->getUrlKey()) === strtolower($cityUrlKey);
        });

        return $filtered->first();
    }

    /**
     * @param string $type
     * @param string $city
     * @param string $postcode
     * @param string $address
     * @param string $street
     * @param string $building
     *
     * @return array
     */
    protected function createFormattedLocation($type, $city, $postcode, $address, $street = '', $building = '')
    {
        // this is to handle the case when the user select district / main area without a street address
        $deliveryAddress = trim(sprintf('%s %s, %s', $building, $street, $postcode));
        $deliveryAddress = strpos($deliveryAddress, ',') === 0 ? substr($deliveryAddress, 1) : $deliveryAddress;

        return [
            'type'             => $type,
            'city'             => $city,
            'postcode'         => $postcode,
            'address'          => urldecode($address),
            'street'           => urldecode($street),
            'building'         => urldecode($building),
            'delivery_address' => urldecode($deliveryAddress)
        ];
    }
}
