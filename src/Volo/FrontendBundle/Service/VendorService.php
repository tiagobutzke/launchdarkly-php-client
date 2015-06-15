<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Foodpanda\ApiSdk\Provider\VendorProvider;

class VendorService
{
    const VENDOR_ID_CACHE_KEY_PREFIX = 'vendorId::';
    /**
     * @var CityProvider
     */
    protected $cityProvider;

    /**
     * @var VendorProvider
     */
    protected $vendorProvider;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param CityProvider   $cityProvider
     * @param VendorProvider $vendorProvider
     * @param Cache          $cache
     */
    public function __construct(CityProvider $cityProvider, VendorProvider $vendorProvider, Cache $cache)
    {
        $this->cityProvider   = $cityProvider;
        $this->vendorProvider = $vendorProvider;
        $this->cache          = $cache;
    }

    /**
     * This method returns the vendor code by urlKey
     *
     * All the codes are cached with timeLife = 0
     *
     * The data could be outdated
     *
     * @param string $urlKey
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getVendorCodeByUrlKey($urlKey)
    {
        if (!$this->cache->contains($urlKey)) {
            $this->refreshCachedVendorCode();
        }

        if (!$this->cache->contains($urlKey)) {
            throw new \RuntimeException(sprintf('Vendor not found for urlKey "%s".', $urlKey));
        }

        return $this->cache->fetch($urlKey);
    }

    /**
     * This method returns the vendor code by id
     *
     * All the codes are cached with timeLife = 0
     *
     * The data could be outdated
     *
     * @param string $id
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getVendorCodeById($id)
    {
        $key = static::VENDOR_ID_CACHE_KEY_PREFIX . $id;

        if (!$this->cache->contains($key)) {
            $this->refreshCachedVendorCode();
        }

        if (!$this->cache->contains($key)) {
            throw new \RuntimeException(sprintf('Vendor not found for id "%s".', $id));
        }

        return $this->cache->fetch($key);
    }
    /**
     *
     */
    public function refreshCachedVendorCode()
    {
        $cities = $this->cityProvider->findAll();
        foreach ($cities->getItems() as $city) {
            foreach ($this->vendorProvider->findVendorsByCity($city)->getItems() as $vendor) {
                /** @var $vendor Vendor */
                $this->cache->save($vendor->getUrlKey(), $vendor->getCode());
                $this->cache->save(
                    static::VENDOR_ID_CACHE_KEY_PREFIX . $vendor->getId(),
                    [
                        'code' => $vendor->getCode(),
                        'urlKey' => $vendor->getUrlKey(),
                    ]
                );
            }
        }
    }

}
