<?php

namespace Volo\FrontendBundle\EventListener;

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

    public function setDefaultTimeZone()
    {
        date_default_timezone_set($this->timeZone);
    }
}
