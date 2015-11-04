<?php
namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Foodpanda\Bundle\Core\HealthBundle\Model\Status;
use Foodpanda\Bundle\Core\HealthBundle\Interfaces\CheckInterface;
use Doctrine\Common\Cache\RedisCache;

class CacheService implements CheckInterface
{
    use StatsTrait;

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
        foreach ($this->doGetStats($this->redisCache->getRedis()->info()) as $k => $v) {
            $this->status->addMessage($k . ': ' . $v);
        }

        return $this->status;
    }
}
