<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Foodpanda\ApiSdk\Entity\Cms\CmsItem;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Foodpanda\ApiSdk\Provider\CmsProvider;

class CmsService
{
    /**
     * @var CmsProvider
     */
    private $cmsProvider;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param CmsProvider $cmsProvider
     * @param Cache $cache
     */
    public function __construct(CmsProvider $cmsProvider, Cache $cache)
    {
        $this->cmsProvider = $cmsProvider;
        $this->cache = $cache;
    }

    /**
     * @param string $code
     * @param bool $caseSensitive
     *
     * @return CmsItem
     * @throws EntityNotFoundException
     */
    public function findByCode($code, $caseSensitive = true)
    {
        if (!$this->cache->contains($code)) {
            $cmsItem = $this->cmsProvider->findByCode($code, $caseSensitive);
            $this->cache->save($code, $cmsItem, 300); // temporary workaround

            return $cmsItem;
        }

        return $this->cache->fetch($code);
    }
}
