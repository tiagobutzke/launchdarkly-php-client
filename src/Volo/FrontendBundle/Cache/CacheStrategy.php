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

        return self::isVendorWithLatLng($request, $resource) || self::isCms($resource);
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

    /**
     * @param RequestInterface $request
     * @param string $resource
     *
     * @return bool
     */
    private static function isVendorWithLatLng(RequestInterface $request, $resource)
    {
        return 'vendors' === $resource
            && ($request->getQuery()->hasKey('latitude')
            && $request->getQuery()->hasKey('longitude'));
    }

    /**
     * @param string $resource
     *
     * @return bool
     */
    private static function isCms($resource)
    {
        return 'cms' === $resource;
    }
}
