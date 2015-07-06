<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Doctrine\Common\Collections\ArrayCollection;
use Volo\FrontendBundle\Service\ScheduleService;

class VendorExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var ScheduleService
     */
    protected $scheduleService;

    /**
     * @param string          $locale
     * @param ScheduleService $scheduleService
     */
    public function __construct($locale, ScheduleService $scheduleService)
    {
        $this->locale          = $locale;
        $this->scheduleService = $scheduleService;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getTimePickerValues', array($this, 'getTimePickerValues')),
            new \Twig_SimpleFunction('getTimePickerTimeValues', array($this, 'getTimePickerTimeValues')),
            new \Twig_SimpleFunction('isVendorOpen', array($this->scheduleService, 'isVendorOpen')),
            new \Twig_SimpleFunction('getDailySchedules', array($this->scheduleService, 'getDailySchedules')),
            new \Twig_SimpleFunction('getNextDayPeriods', array($this->scheduleService, 'getNextDayPeriods')),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vendor_extension';
    }

    /**
     * @param ArrayCollection $times
     *
     * @return array
     */
    public function getTimePickerTimeValues(ArrayCollection $times)
    {
        $intl = \IntlDateFormatter::create($this->locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);

        $results = [];
        foreach ($times as $time) {
            $endTime = clone $time;
            $results[$time->format('H:i')] = sprintf(
                '%s - %s',
                $intl->format($time),
                $intl->format($endTime->modify('+30 minutes'))
            );
        }

        return $results;
    }

    /**
     * @param Vendor    $vendor
     * @param \DateTime $start
     *
     * @return ArrayCollection
     */
    public function getTimePickerValues(Vendor $vendor, \DateTime $start)
    {
        /** @var ArrayCollection $openingPeriods */
        // We exclude the last closing interval of all periods by adding a 1 min offset.
        $openingPeriods = $this->scheduleService->getNextFourDaysOpenings($vendor, $start, $vendor->getMinimumDeliveryTime(), -1);

        if ($openingPeriods->isEmpty()) {
            return new ArrayCollection();
        }

        // Prune the elements in the past.
        $firstPeriod = current($openingPeriods->slice(0, 1));
        if ($firstPeriod->first()->format('Y-m-d') === date('Y-m-d')) {
            $startPlusDelivery = clone $start;
            $startPlusDelivery->modify(sprintf('+%s minutes', $vendor->getMinimumDeliveryTime()));
            $firstPeriod = $firstPeriod->filter(function (\DateTime $time) use ($startPlusDelivery) {
                return $time > $startPlusDelivery;
            });
        }

        if ($firstPeriod->count() === 0 && !$this->scheduleService->isVendorOpen($vendor, $start)) {
            return $openingPeriods->slice(1, 3);
        } else {
            return new ArrayCollection([$firstPeriod] + $openingPeriods->slice(1, 2));
        }
    }
}
