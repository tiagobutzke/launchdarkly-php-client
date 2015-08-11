<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Foodpanda\ApiSdk\Entity\Product\Product;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
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
     * @var ScheduleService
     */
    private $scheduleService;

    /**
     * @param CityProvider $cityProvider
     * @param VendorProvider $vendorProvider
     * @param ScheduleService $scheduleService
     * @param Cache $cache
     */
    public function __construct(
        CityProvider $cityProvider,
        VendorProvider $vendorProvider,
        ScheduleService $scheduleService,
        Cache $cache
    ) {
        $this->cityProvider = $cityProvider;
        $this->vendorProvider = $vendorProvider;
        $this->cache = $cache;
        $this->scheduleService = $scheduleService;
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
        $lowercaseUrlKey = strtolower($urlKey);
        if (!$this->cache->contains($lowercaseUrlKey)) {
            $this->refreshCachedVendorCode();
        }

        if (!$this->cache->contains($lowercaseUrlKey)) {
            throw new \RuntimeException(sprintf('Vendor not found for urlKey "%s".', $urlKey));
        }

        return $this->cache->fetch($lowercaseUrlKey);
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
        $now = new \DateTime('now');
        $cities = $this->cityProvider->findAll();
        foreach ($cities->getItems() as $city) {
            foreach ($this->vendorProvider->findVendorsByCity($city)->getItems() as $vendor) {
                $this->scheduleService->getNextDayPeriods($vendor, $now);
                /** @var $vendor Vendor */
                $this->cache->save(strtolower($vendor->getUrlKey()), $vendor->getCode());
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

    /**
     * @param $vendorId
     * @param $variationId
     *
     * @throws \RuntimeException
     * @return Product
     */
    public function getProduct($vendorId, $variationId)
    {
        try {
            $vendor = $this->vendorProvider->find($vendorId);
        } catch (ApiErrorException $exception) {
            throw new \RuntimeException(sprintf('Vendor %s not found!', $vendorId));
        }

        foreach ($vendor->getMenus() as $menu) {
            foreach ($menu->getMenuCategories() as $category) {
                /** @var Product $product */
                foreach ($category->getProducts() as $product) {
                    foreach ($product->getProductVariations() as $variation) {
                        if ($variation->getId() === $variationId) {
                            return $product;
                        }
                    }
                }
            }
        }

        throw new \RuntimeException(sprintf('Product with variation %s not found!', $variationId));
    }
}
