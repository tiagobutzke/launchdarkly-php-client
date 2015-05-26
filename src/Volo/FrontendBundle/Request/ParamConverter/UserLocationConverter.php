<?php

namespace Volo\FrontendBundle\Request\ParamConverter;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\Cart\AreaLocation;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Volo\FrontendBundle\Service\CustomerLocationService;

class UserLocationConverter implements ParamConverterInterface
{
    /**
     * CityProvider
     */
    protected $cityProvider;

    /**
     * @var CustomerLocationService
     */
    protected $customerLocationService;

    /**
     * @param CityProvider $cityProvider
     * @param CustomerLocationService $customerLocationService
     */
    public function __construct(CityProvider $cityProvider, CustomerLocationService $customerLocationService)
    {
        $this->cityProvider = $cityProvider;
        $this->customerLocationService = $customerLocationService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $object = null;

        $cityId = $request->attributes->get('city_id', false);
        $areaId = $request->attributes->get('area_id', false);
        $lat = $request->attributes->get('lat', false);
        $lng = $request->attributes->get('lng', false);

        switch(true) {
            case $cityId:
                $object = new CityLocation($cityId);
                break;
            case $areaId:
                $object = new AreaLocation($cityId);
                break;
            case $lat && $lng:
                $object = new GpsLocation($lat, $lng);

                $sessionId = $request->getSession()->getId();

                $location = $this->customerLocationService->create(
                    $request->get(CustomerLocationService::KEY_FORMATTED_ADDRESS),
                    $request->get(CustomerLocationService::KEY_LAT),
                    $request->get(CustomerLocationService::KEY_LNG),
                    $request->get(CustomerLocationService::KEY_POSTAL_INDEX)
                );

                $this->customerLocationService->set($sessionId, $location);
                break;
            default:
                $message = 'Please supply keys `city_id` or `area_id` or `latitude` and `longitude`.';
                throw new BadRequestHttpException($message);
        }

        $request->attributes->set('location', $object);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === AbstractLocation::class;
    }
}
