<?php

namespace Volo\FrontendBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class DefaultTimeZoneListener
{
    /**
     * @var string
     */
    protected $timeZone;

    /**
     * @param string $timeZone
     */
    public function __construct($timeZone)
    {
        $this->timeZone = $timeZone;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function setDefaultTimeZone(GetResponseEvent $event)
    {
        date_default_timezone_set($this->timeZone);
    }
}
