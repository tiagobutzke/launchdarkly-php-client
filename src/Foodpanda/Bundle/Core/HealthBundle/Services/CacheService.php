<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Doctrine\Common\Cache\RedisCache;

class CacheService implements CheckInterface
{
    /**
     * @var RedisCache
     */
    protected $redisCache;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @param RedisCache $redisCache
     */
    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
        $this->status = new Status();
    }

    /**
     * @return bool
     */
    public function check()
    {
        $cacheKey = uniqid('cache_test_key', true);
        $this->redisCache->save($cacheKey, $cacheKey, 5);
        $storedValue = $this->redisCache->fetch($cacheKey);
        $this->status->setStatus($storedValue === $cacheKey);
        return $this->status;
    }
}
