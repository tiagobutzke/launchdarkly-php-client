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

        $cityCode = $request->attributes->get('cityUrlKey', false);
        $areaId = $request->attributes->get('area_id', false);
        $lat = $request->attributes->get('latitude', false);
        $lng = $request->attributes->get('longitude', false);

        switch(true) {
            case $cityCode:
                $object = $this->createParameterByCityCode($cityCode);
                break;
            case $areaId:
                $object = new AreaLocation($areaId);
                break;
            case $lat && $lng:
                $object = new GpsLocation($lat, $lng);

                $this->saveLocationToCache($request);
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

    /**
     * @param string $cityCode
     *
     * @return CityLocation
     */
    public function createParameterByCityCode($cityCode)
    {
        $object = null;

        $items = $this->cityProvider->findAll()->getItems();
        foreach ($items as $item) {
            if ($item->getUrlKey() === $cityCode) {
                $object = new CityLocation($item->getId());
                break;
            }
        }

        return $object;
    }

    /**
     * @param Request $request
     */
    public function saveLocationToCache(Request $request)
    {
        $sessionId = $request->getSession()->getId();

        $location = $this->customerLocationService->create(
            $request->get(CustomerLocationService::KEY_LAT),
            $request->get(CustomerLocationService::KEY_LNG),
            $request->get(CustomerLocationService::KEY_PLZ)
        );

        $this->customerLocationService->set($sessionId, $location);
    }
}
