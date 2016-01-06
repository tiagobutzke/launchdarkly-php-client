<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\CacheProvider;
use Foodpanda\ApiSdk\Entity\Cms\CmsItem;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Foodpanda\ApiSdk\Provider\CmsProvider;

class CmsService
{
    const CACHE_CMS_BLOCK_TTL = 172800;

    /**
     * @var CmsProvider
     */
    private $cmsProvider;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var CacheProvider
     */
    private $cacheAll;

    /**
     * @param CmsProvider $cmsProvider
     * @param CacheProvider $cache
     * @param CacheProvider $cacheAll
     */
    public function __construct(CmsProvider $cmsProvider, CacheProvider $cache, CacheProvider $cacheAll)
    {
        $this->cmsProvider = $cmsProvider;
        $this->cache = $cache;
        $this->cacheAll = $cacheAll;
    }

    /**
     * @param string $code
     *
     * @return CmsItem
     * @throws EntityNotFoundException
     */
    public function findByCode($code)
    {
        $code = strtolower($code);
        $element = null;
        if (!$this->cacheAll->contains('all')) {
            $cmsItems = $this->cmsProvider->findAll();

            foreach ($cmsItems as $item) {
                $this->cache->save(strtolower($item->getCode()), $item, self::CACHE_CMS_BLOCK_TTL);
            }

            $this->cacheAll->save('all', 1, self::CACHE_CMS_BLOCK_TTL);
        }

        if ($this->cache->contains($code)) {
            $element = $this->cache->fetch($code);
        }

        if (null === $element) {
            throw new EntityNotFoundException();
        }

        return $element;
    }

    public function clearCmsCache()
    {
        $this->cacheAll->delete('all');
    }
}
