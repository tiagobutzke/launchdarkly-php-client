<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;

class AppVersionCookieFactory
{

    const COOKIE_NAME = 'AppVersion';
    const COOKIE_EXPIRATION_DAYS = 30;
    const SECONDS_IN_A_DAY = 86400;

    /**
     * @param string $appVersion
     * @return Cookie
     */
    public function get($appVersion)
    {
        return new Cookie(self::COOKIE_NAME, $appVersion, time() + self::COOKIE_EXPIRATION_DAYS * self::SECONDS_IN_A_DAY, '/', null, false, false);
    }

}
