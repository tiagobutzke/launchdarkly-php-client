<?php

namespace Volo\FrontendBundle\Twig;

use Volo\FrontendBundle\Service\ScheduleService;

class VendorExtension extends \Twig_Extension
{
    /**
     * @var ScheduleService
     */
    protected $scheduleService;

    /**
     * @param ScheduleService $scheduleService
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('isVendorOpen', array($this->scheduleService, 'isVendorOpen')),
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
