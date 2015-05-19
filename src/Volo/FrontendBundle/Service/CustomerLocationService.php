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

    const KEY_LAT = 'lat';
    const KEY_LNG = 'lng';
    const KEY_FORMATTED_ADDRESS = 'formatted_address';
    const KEY_POSTAL_INDEX = 'post_code';

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
     * @param string $address
     * @param float $lat
     * @param float $lng
     * @param string $postalIndex
     *
     * @return array
     */
    public function create($address, $lat, $lng, $postalIndex)
    {
        return [
            static::KEY_FORMATTED_ADDRESS => $address,
            static::KEY_LAT => $lat,
            static::KEY_LNG => $lng,
            static::KEY_POSTAL_INDEX => $postalIndex,
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
            [static::KEY_LAT, static::KEY_LNG, static::KEY_FORMATTED_ADDRESS, static::KEY_POSTAL_INDEX],
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
