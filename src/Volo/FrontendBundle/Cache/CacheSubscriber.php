<?php

namespace Volo\FrontendBundle\Cache;

use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber as BaseCacheSubscriber;

class CacheSubscriber extends  BaseCacheSubscriber
{
    /**
     * Checks if the request and response can be cached, and if so, store it.
     *
     * @param CompleteEvent $event
     */
    public function onComplete(CompleteEvent $event)
    {
        $response = $event->getResponse();
        $cacheControl = $response->getHeader('cache-control');
        // Workaround to add valid Cache-Control header to make the response private
        if ('no-cache' === $cacheControl) {
            $cacheControl = 'private, no-cache, no-store';
            $response->setHeader('Cache-Control', $cacheControl);
        }

        parent::onComplete($event);
    }

}
