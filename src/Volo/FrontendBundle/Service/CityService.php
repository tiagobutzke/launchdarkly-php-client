<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Volo\FrontendBundle\Exception\CityNotFoundException;

class CityService
{
    /**
     * @var CityProvider
     */
    protected $cityProvider;

    /**
     * @param CityProvider $cityProvider
     */
    public function __construct(CityProvider $cityProvider)
    {
        $this->cityProvider = $cityProvider;
    }


    /**
     * @param string $cityUrlKey
     *
     * @return City
     */
    public function findCityByCode($cityUrlKey)
    {
        $filtered = $this->cityProvider->findAll()->getItems()->filter(function($element) use ($cityUrlKey) {
            /** @var City $element */
            return strtolower($element->getUrlKey()) === strtolower($cityUrlKey);
        });

        $city = $filtered->first();
        if ($city === false) {
            throw new CityNotFoundException(sprintf('City with the url key "%s" is not found', $cityUrlKey));
        }

        return $city;
    }

    /**
     * @param $id
     *
     * @return City
     */
    public function findCityById($id)
    {
        $city = $this->cityProvider->find($id);

        if ($city === false) {
            throw new CityNotFoundException(sprintf('City with the id "%s" is not found', $id));
        }

        return $city;
    }

    /**
     * @param GpsLocation $location
     *
     * @return City
     */
    public function findCityByGpsLocation(GpsLocation $location)
    {
        try {
            $cities = $this->cityProvider->findCitiesByLocation($location);
        } catch (ApiErrorException $e) {
            throw new \RuntimeException(
                sprintf('No cities found with coordinates : %f/%f', $location->getLatitude(), $location->getLongitude())
            );
        }

        if ($cities->getAvailableCount() === 0) {
            throw new \RuntimeException(
                sprintf('No cities found with coordinates : %f/%f', $location->getLatitude(), $location->getLongitude())
            );
        }

        return $cities->getItems()->first();
    }
}
