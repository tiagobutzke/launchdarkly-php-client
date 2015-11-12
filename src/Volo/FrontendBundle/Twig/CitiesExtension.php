<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Foodpanda\ApiSdk\Entity\City\CitiesCollection;

class CitiesExtension extends \Twig_Extension
{
    /**
     * @var CityProvider
     */
    protected $cityApiProvider;

    /**
     * @param CityProvider $cityApiProvider
     */
    public function __construct(CityProvider $cityApiProvider)
    {
        $this->cityApiProvider = $cityApiProvider;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getCities', [$this, 'getCities']),
            new \Twig_SimpleFunction('getFilteredCities', [$this, 'getFilteredCities']),
        ];
    }

    /**
     * @return CitiesCollection|City[]
     */
    public function getCities()
    {
        try {
            $cities = $this->cityApiProvider->findAll()->getItems();
        } catch (EntityNotFoundException $exception) {
            $cities = [];
        }

        return $cities;
    }

    /**
     * @return CitiesCollection|City[]
     */
    public function getFilteredCities()
    {
        try {
            $cities = $this->cityApiProvider->findAll()->getItems()->filter(function ($city) {
                /** @var City $city */
                return preg_match('/(testorf|\sold$)/i', $city->getName()) === 0;
            });
        } catch (EntityNotFoundException $exception) {
            $cities = [];
        }

        return $cities;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cities_extension';
    }
}
