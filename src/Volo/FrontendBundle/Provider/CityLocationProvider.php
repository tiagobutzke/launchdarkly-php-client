<?php

namespace Volo\FrontendBundle\Provider;

use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Volo\FrontendBundle\Exception\CityNotFoundException;

/**
 * Temporary class created to solve Flood Feature v2.0
 *
 * Class CityLocationProvider
 * @package Volo\FrontendBundle\Provider
 * @link https://jira.rocket-internet.de/browse/INTVOLO-1798
 */
class CityLocationProvider
{
    /**
     * array
     */
    protected $citiesGpsLocations = [
        // at
        'dubai' => ['lat' => 25.2048493, 'lng' => 55.2707828],
        // au
        'sydney' => ['lat' => -33.8674769, 'lng' => 151.2069776],
        'melbourne' => ['lat' => -37.815018, 'lng' => 144.946014],
        // at
        'wien' => ['lat' => 48.2081743, 'lng' => 16.3738189],
        // de
        'berlin' => ['lat' => 52.52000659999999, 'lng' => 13.404954],
        'frankfurt' => ['lat' => 50.1109221, 'lng' => 8.6821267],
        'hamburg' => ['lat' => 53.5510846, 'lng' => 9.9936818],
        'muenchen' => ['lat' => 48.1351253, 'lng' => 11.5819806],
        'duesseldorf' => ['lat' => 51.2277411, 'lng' => 6.7734556],
        'koeln' => ['lat' => 50.937531, 'lng' => 6.9602786],
        'stuttgart' => ['lat' => 48.7758459, 'lng' => 9.1829321],
        // es
        'barcelona' => ['lat' => 41.3850639, 'lng' => 2.1734035],
        'madrid' => ['lat' => 40.4167754, 'lng' => -3.7037902],
        // fi
        'helsinki' => ['lat' => 60.17332440000001, 'lng' => 24.9410248],
        // fr
        'paris' => ['lat' => 48.856614, 'lng' => 2.3522219],
        'lyon' => ['lat' => 45.764043, 'lng' => 4.835659],
        // it
        'milano' => ['lat' => 45.4654219, 'lng' => 9.1859243],
        'torino' => ['lat' => 45.070312, 'lng' => 7.686856499999999],
        // nl
        'amsterdam' => ['lat' => 52.3702157, 'lng' => 4.895167900000001],
        // no
        'oslo' => ['lat' => 59.9138688, 'lng' => 10.7522454],
        // se
        'stockholm' => ['lat' => 59.32932349999999, 'lng' => 18.0685808],
    ];


    /**
     * @param string $cityUrlKey
     *
     * @return GpsLocation
     */
    public function findGpsLocationByCode($cityUrlKey)
    {
        if (!array_key_exists($cityUrlKey, $this->citiesGpsLocations)) {
            throw new CityNotFoundException();
        }

        return new GpsLocation(
            $this->citiesGpsLocations[$cityUrlKey]['lat'],
            $this->citiesGpsLocations[$cityUrlKey]['lng']
        );
    }
}
