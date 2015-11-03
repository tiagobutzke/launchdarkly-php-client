<?php

namespace Volo\FrontendBundle\Cache;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Subscriber\Cache\Utils;

class CacheStrategy
{
    /**
     * Determine if a request can be cached.
     *
     * @param RequestInterface $request Request to check
     * @return bool
     */
    public static function canCacheRequest(RequestInterface $request)
    {
        if (self::isExcludedFromCache($request)) {
            return false;
        }

        return Utils::canCacheRequest($request);
    }

    /**
     * Determine if a request should be excluded from caching
     *
     * @param RequestInterface $request
     * @return bool
     */
    private static function isExcludedFromCache(RequestInterface $request)
    {
        $resource = self::extractResource($request->getPath());

        return 'vendors' === $resource
           && ($request->getQuery()->hasKey('latitude') && $request->getQuery()->hasKey('longitude'));
    }

    /**
     *  Extract from /api/v4/vendors -> vendors
     *
     * @param string $resource
     * @return string
     */
    private static function extractResource($resource)
    {
        return preg_replace('/^\/api\/v\d./', '', $resource);
    }
}
