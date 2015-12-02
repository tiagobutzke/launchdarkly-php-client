<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;

class AppVersionCookieFactory
{

    const COOKIE_NAME = 'AppVersion';

    /**
     * @param string $appVersion
     * @return Cookie
     */
    public function get($appVersion)
    {
        return new Cookie(self::COOKIE_NAME, $appVersion, time() + 30 * 86400, '/', null, false, false);
    }

}
