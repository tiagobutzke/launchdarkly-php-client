<?php

namespace Volo\FrontendBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Volo\FrontendBundle\Service\VendorService;

class MigrationCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var VendorService
     */
    private $vendorService;

    public function __construct($vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $this->vendorService->refreshCachedVendorCode();
    }
}
