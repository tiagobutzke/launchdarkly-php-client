<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Provider\VendorProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @ParamConverter("location", converter="user_location_converter")
     *
     *
     * @param Request $request
     * @param AbstractLocation $location
     *
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function listAction(Request $request, AbstractLocation $location)
    {
        $cuisines = $request->query->get('cuisine');
        $foodCharacteristics = $request->query->get('food_characteristic');
        $includes = $request->query->get('includes', ['cuisines', 'food_characteristics']);

        $vendors = $this->getVendorProvider()->findVendorsByLocation(
            $location,
            $includes,
            $cuisines,
            $foodCharacteristics
        );

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
     * @return VendorProvider
     */
    private function getVendorProvider()
    {
        return $this->get('volo_frontend.provider.vendor');
    }
}
