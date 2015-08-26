<?php

namespace Volo\FrontendBundle\Request\ParamConverter;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\Cart\AreaLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @var Translator
     */
    protected $translator;

    /**
     * @param CityProvider $cityProvider
     * @param CustomerLocationService $customerLocationService
     * @param Translator $translator
     */
    public function __construct(
        CityProvider $cityProvider,
        CustomerLocationService $customerLocationService,
        Translator $translator
    ) {
        $this->cityProvider = $cityProvider;
        $this->customerLocationService = $customerLocationService;
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $convertedParameter = null;
        $formattedLocation = [];

        $cityUrlKey = $request->attributes->get('cityUrlKey', false);
        $areaId = $request->attributes->get('area_id', false);
        $lat = $request->attributes->get('latitude', false);
        $lng = $request->attributes->get('longitude', false);

        switch(true) {
            case $cityUrlKey:
                $city = $this->findCityByCode($cityUrlKey);
                if ($city === false) {
                    throw new NotFoundHttpException(sprintf('City with the url key "%s" is not found', $cityUrlKey));
                }

                $convertedParameter = new CityLocation($city->getId());
                $formattedLocation = $this->createFormattedLocation(
                    $convertedParameter->getLocationType(),
                    $city->getName(),
                    null,
                    null
                );

                $request->attributes->set('cityObj', $city);
                break;
            case $areaId:
                $convertedParameter = new AreaLocation($areaId);
                break;
            case is_numeric($lat) && is_numeric($lng):
                $convertedParameter = new GpsLocation($lat, $lng);

                $gpsLocation = $this->customerLocationService->create(
                    $request->get(CustomerLocationService::KEY_LAT),
                    $request->get(CustomerLocationService::KEY_LNG),
                    $request->get(CustomerLocationService::KEY_PLZ),
                    $request->get(CustomerLocationService::KEY_CITY),
                    $request->get(CustomerLocationService::KEY_ADDRESS),
                    $request->get(CustomerLocationService::KEY_STREET),
                    $request->get(CustomerLocationService::KEY_BUILDING)
                );

                $this->customerLocationService->set($request->getSession(), $gpsLocation);
                $formattedLocation = $this->createFormattedLocation(
                    $convertedParameter->getLocationType(),
                    $gpsLocation[CustomerLocationService::KEY_CITY],
                    $gpsLocation[CustomerLocationService::KEY_PLZ],
                    $gpsLocation[CustomerLocationService::KEY_ADDRESS],
                    $gpsLocation[CustomerLocationService::KEY_STREET],
                    $gpsLocation[CustomerLocationService::KEY_BUILDING]
                );

                try {
                    $city = $this->findCityByLocation($convertedParameter);
                    $request->attributes->set('cityObj', $city);
                } catch (NotFoundHttpException $e) {
                    // no city ? we just continue
                }
                break;
            default:
                $message = $this->translator->trans('customer.location.missing_keys');
                throw new BadRequestHttpException($message);
        }

        $request->attributes->set('location', $convertedParameter);
        $request->attributes->set('formattedLocation', $formattedLocation);

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
     * @param string $cityUrlKey
     *
     * @return City
     */
    protected function findCityByCode($cityUrlKey)
    {
        $filtered = $this->cityProvider->findAll()->getItems()->filter(function($element) use ($cityUrlKey) {
            /** @var City $element */
            return strtolower($element->getUrlKey()) === strtolower($cityUrlKey);
        });

        return $filtered->first();
    }

    /**
     * @param GpsLocation $location
     *
     * @return City
     */
    protected function findCityByLocation(GpsLocation $location)
    {
        try {
            $cities = $this->cityProvider->findCitiesByLocation($location);
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
        $deliveryAddress = strpos($deliveryAddress, ',') ? substr($deliveryAddress, 1) : $deliveryAddress;

        return [
            'type'             => $type,
            'city'             => $city,
            'postcode'         => $postcode,
            'address'          => $address,
            'street'           => $street,
            'building'         => $building,
            'delivery_address' => $deliveryAddress
        ];
    }
}
