<?php

namespace Foodpanda\Bundle\Core\HealthBundle\Services;

use Doctrine\Common\Cache\Cache;

trait StatsTrait
{
    protected function doGetStats(array $info)
    {
        return [
            Cache::STATS_HITS             => $info['keyspace_hits'],
            Cache::STATS_MISSES           => $info['keyspace_misses'],
            Cache::STATS_UPTIME           => $info['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE     => $info['used_memory'],
            'memory_usage_human'          => $info['used_memory_human'],
            Cache::STATS_MEMORY_AVAILABLE => false,
        ];
    }
}
