<?php

namespace Volo\FrontendBundle\Twig;

use Volo\FrontendBundle\Service\EventService;
use Volo\FrontendBundle\Service\ScheduleService;

class VendorExtension extends \Twig_Extension
{
    /**
     * @var ScheduleService
     */
    protected $scheduleService;

    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * @param ScheduleService $scheduleService
     * @param EventService $eventService
     */
    public function __construct(ScheduleService $scheduleService, EventService $eventService)
    {
        $this->scheduleService = $scheduleService;
        $this->eventService = $eventService;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('isVendorOpen', array($this->scheduleService, 'isVendorOpen')),
            new \Twig_SimpleFunction('isVendorFloodFeatureClosed', array($this->eventService, 'isVendorFloodFeatureClosed')),
            new \Twig_SimpleFunction('getDailySchedules', array($this->scheduleService, 'getDailySchedules')),
            new \Twig_SimpleFunction('getNextDayPeriods', array($this->scheduleService, 'getNextDayPeriods')),
            new \Twig_SimpleFunction('getTimePickerJsonValues', array($this->scheduleService, 'getTimePickerJsonValues')),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vendor_extension';
    }
}
