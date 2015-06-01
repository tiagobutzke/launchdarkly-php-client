<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Volo\FrontendBundle\Exception\Location\MissingKeysException;

class CustomerLocationService
{
    /**
     * @var Cache
     */
    protected $cache;

    const SESSION_KEY_PREFIX = 'customer_locations:';

    const KEY_LAT = 'latitude';
    const KEY_LNG = 'longitude';
    const KEY_PLZ = 'postcode';
    const KEY_CITY = 'city';
    const KEY_ADDRESS = 'address';

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $sessionId
     * @param array $location
     */
    public function set($sessionId, array $location)
    {
        $this->validate($location);
        $this->cache->save($this->createSessionKey($sessionId), $location);
    }

    /**
     * @param string $sessionId
     *
     * @return array
     */
    public function get($sessionId)
    {
        $value = $this->cache->fetch($this->createSessionKey($sessionId));

        return $value;
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

    /**
     * @param string $sessionId
     *
     * @return string
     */
    protected function createSessionKey($sessionId)
    {
        return sprintf('%s:%s', static::SESSION_KEY_PREFIX, $sessionId);
    }
}
