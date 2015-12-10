<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AppVersionListener
{
    /**
     * @var Cookie
     */
    protected $cookie;

    /**
     * @param Cookie $cookie
     *
     */
    public function __construct(Cookie $cookie) {
        $this->cookie = $cookie;
    }

    /**
     * @param FilterResponseEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->setCookie($this->cookie);
    }

}
