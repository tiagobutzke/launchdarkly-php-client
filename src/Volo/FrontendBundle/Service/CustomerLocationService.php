<?php

namespace Volo\FrontendBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Volo\FrontendBundle\Exception\Location\MissingKeysException;

class CustomerLocationService
{
    const SESSION_KEY_PREFIX = 'customer::locations';

    const KEY_LAT = 'latitude';
    const KEY_LNG = 'longitude';
    const KEY_PLZ = 'postcode';
    const KEY_CITY = 'city';
    const KEY_ADDRESS = 'address';

    /**
     * @param SessionInterface $session
     * @param array $location
     */
    public function set(SessionInterface $session, array $location)
    {
        $this->validate($location);

        $session->set(static::SESSION_KEY_PREFIX, $location);
    }

    /**
     * @param SessionInterface $session
     *
     * @return array
     */
    public function get(SessionInterface $session)
    {
        return $session->get(static::SESSION_KEY_PREFIX, $this->create(null, null, null, null, null));
    }

    /**
     * @param float $lat
     * @param float $lng
     * @param string $postCode
     * @param string $city
     * @param string $address
     *
     * @return array
     */
    public function create($lat, $lng, $postCode, $city, $address)
    {
        return [
            static::KEY_LAT => $lat,
            static::KEY_LNG => $lng,
            static::KEY_PLZ => $postCode,
            static::KEY_CITY => $city,
            static::KEY_ADDRESS => $address,
        ];
    }

    /**
     * @param array $location
     *
     * @throws MissingKeysException
     */
    protected function validate(array $location)
    {
        $missingKeys = array_diff(
            [static::KEY_LAT, static::KEY_LNG, static::KEY_PLZ, static::KEY_CITY],
            array_keys(array_filter($location))
        );

        if (count($missingKeys) > 0) {
            throw new MissingKeysException($missingKeys);
        }
    }
}
