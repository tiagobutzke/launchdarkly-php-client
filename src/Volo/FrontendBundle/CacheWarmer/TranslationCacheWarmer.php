<?php

namespace Volo\FrontendBundle\CacheWarmer;

use Foodpanda\Bundle\WebTranslateItBundle\Services\SyncService;
use Foodpanda\WebTranslateIt\Model\StringFilter;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class TranslationCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var SyncService
     */
    protected $translationSyncService;

    /**
     * @var string
     */
    protected $translationLabel;

    /**
     * @param SyncService $translationSyncService
     * @param string      $translationLabel
     */
    public function __construct(SyncService $translationSyncService, $translationLabel)
    {
        $this->translationSyncService = $translationSyncService;
        $this->translationLabel       = $translationLabel;
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
        $stringFilter = new StringFilter();
        $stringFilter->addLabel($this->translationLabel);
        $this->translationSyncService->syncTranslations($stringFilter);
    }
}
