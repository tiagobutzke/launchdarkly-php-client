<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Foodpanda\ApiSdk\Entity\Cart\LocationInterface;
use Foodpanda\ApiSdk\Entity\Product\Product;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;
use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Foodpanda\ApiSdk\Provider\VendorProvider;
use Symfony\Component\Translation\TranslatorInterface;
use Volo\FrontendBundle\Twig\VoloExtension;

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
     * @var ThumborService
     */
    private $thumborService;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var VoloExtension
     */
    protected $voloExtension;

    /**
     * @param CityProvider $cityProvider
     * @param VendorProvider $vendorProvider
     * @param ScheduleService $scheduleService
     * @param Cache $cache
     * @param ThumborService $thumborService
     * @param TranslatorInterface $translator
     * @param VoloExtension $voloExtension
     */
    public function __construct(
        CityProvider $cityProvider,
        VendorProvider $vendorProvider,
        ScheduleService $scheduleService,
        Cache $cache,
        ThumborService $thumborService,
        TranslatorInterface $translator,
        VoloExtension $voloExtension
    ) {
        $this->cityProvider = $cityProvider;
        $this->vendorProvider = $vendorProvider;
        $this->cache = $cache;
        $this->scheduleService = $scheduleService;
        $this->thumborService = $thumborService;
        $this->translator = $translator;
        $this->voloExtension = $voloExtension;
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
    public function getCachedVendorCodeByUrlKey($urlKey)
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
    public function getCachedVendorCodeById($id)
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

    /**
     * @param LocationInterface $location
     * @param array $includes
     * @param array $cuisines
     * @param array $foodCharacteristics
     *
     * @return VendorsCollection
     */
    public function findAll(
        LocationInterface $location,
        array $includes = ['cuisines', 'metadata', 'food_characteristics'],
        array $cuisines = [],
        array $foodCharacteristics = []
    )
    {
        $vendors = $this->vendorProvider->findVendorsByLocation(
            $location,
            array_merge($includes, ['schedules']),
            $cuisines,
            $foodCharacteristics
        );

        $vendors = $vendors->getItems()->filter(
            function (Vendor $vendor) {
                return !$vendor->getSchedules()->isEmpty();
            }
        );

        $this->updateExtraProperties($vendors);

        return $vendors;
    }

    /**
     * @param Vendor $vendor
     */
    private function updateAvailableInProperty(Vendor $vendor)
    {
        if ($vendor->getMetadata()->getAvailableIn()) {
            $availableIn = new \DateTime($vendor->getMetadata()->getAvailableIn());
            if ($availableIn->format('d-m-Y') == (new \DateTime('now'))->format('d-m-Y')) {
                $message = $this->translator->trans(
                    'restaurant.open_at_time',
                    [
                        '%time%' => $this->voloExtension->formatTime($availableIn)
                    ]
                );
                $vendor->getMetadata()->setAvailableIn($message);
            } else {
                $message = $this->translator->trans(
                    'restaurant.open_at_day_time',
                    [
                        '%time%' => $this->voloExtension->formatTime($availableIn),
                        '%day%' => $this->voloExtension->localisedDay($availableIn)
                    ]
                );
                $vendor->getMetadata()->setAvailableIn($message);
            }
        }
    }

    /**
     * @param VendorsCollection $vendors
     */
    private function updateExtraProperties(VendorsCollection $vendors)
    {
        foreach ($vendors as $vendor) {
            /** @var Vendor $vendor */
            $image = $this->thumborService->generateUrl($vendor, 'vendor_image');
            $vendor->setImageLowResolution((string)$image);

            $image = $this->thumborService->generateUrl($vendor, 'vendor_image_retina');
            $vendor->setImageHighResolution((string)$image);

            $this->updateAvailableInProperty($vendor);
        }
    }
}
