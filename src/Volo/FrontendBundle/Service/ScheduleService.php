<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Schedule\Schedule;
use Foodpanda\ApiSdk\Entity\SpecialDay\SpecialDay;
use Foodpanda\ApiSdk\Entity\Schedule\SchedulesCollection;
use Foodpanda\ApiSdk\Entity\SpecialDay\SpecialDaysCollection;
use Doctrine\Common\Collections\ArrayCollection;

class ScheduleService
{
    /**
     * @param SchedulesCollection $scheduleCollection
     * @param int                 $dayKey
     *
     * @return ArrayCollection
     */
    public function getDailySchedules(SchedulesCollection $scheduleCollection, $dayKey)
    {
        return $scheduleCollection->filter(function (Schedule $element) use ($dayKey) {
            return $element->getWeekday() === (int)$dayKey;
        })->filter(function (Schedule $element) use ($dayKey) {
            return $element->getOpeningType() === 'delivering';
        });
    }

    /**
     * @param SpecialDaysCollection $specialDaysCollection
     * @param \DateTime             $day
     *
     * @return ArrayCollection
     */
    protected function getDailySpecialsDays(SpecialDaysCollection $specialDaysCollection, \DateTime $day)
    {
        return $specialDaysCollection->filter(function (SpecialDay $specialDay) use ($day) {
            return $day->format('Y-m-d') === $specialDay->getDate();
        })->filter(function (SpecialDay $specialDay) use ($day) {
            return $specialDay->getOpeningType() !== 'opened';
        });
    }

    /**
     * @param Vendor $vendor
     * @param bool   $prunePastPeriods
     *
     * @return ArrayCollection
     */
    public function getNextDayPeriods(Vendor $vendor, $prunePastPeriods = false)
    {
        /** @var ArrayCollection $fourDaysPeriod */
        /** @var ArrayCollection $openingAndClosingHours */
        $fourDaysPeriod         = $this->getNextFourDaysOpenings($vendor, new \DateTime());

        if ($fourDaysPeriod->isEmpty()) {
            return new ArrayCollection();
        }

        $openingAndClosingHours = $this->filterOpeningAndClosingHours($vendor, $fourDaysPeriod->first());

        $periods = new ArrayCollection(array_chunk($openingAndClosingHours->toArray(), 2));

        if ($prunePastPeriods) {
            $time = \DateTime::createFromFormat('U', time() + $vendor->getMinimumDeliveryTime() * 60);
            $periods = $periods->filter(function (array $periods) use ($time) {
                return $periods[1] > $time;
            });

            if ($periods->count() === 0) {
                return new ArrayCollection(
                    array_chunk($this->filterOpeningAndClosingHours($vendor, $fourDaysPeriod[1])->toArray(), 2)
                );
            }
        }

        return $periods;
    }

    /**
     * @param Vendor          $vendor
     * @param ArrayCollection $collection
     *
     * @return ArrayCollection
     */
    protected function filterOpeningAndClosingHours(Vendor $vendor, ArrayCollection $collection)
    {
        return $collection->filter(function(\DateTime $time) use ($vendor) {
            $after  = clone $time;
            $before = clone $time;
            $after->modify('+30 minutes');
            $before->modify('-30 minutes');

            $closedBefore = !$this->isVendorOpen($vendor, $before);
            $closedAfter  = !$this->isVendorOpen($vendor, $after);

            return $closedBefore || $closedAfter;
        });
    }

    /**
     * @param Vendor    $vendor
     * @param \DateTime $dateTime
     * @param int       $openingOffset
     * @param int       $closingOffset
     *
     * @return bool
     */
    public function isVendorOpen(Vendor $vendor, \DateTime $dateTime, $openingOffset = 0, $closingOffset = 0)
    {
        $time = (int)$dateTime->format('Hi' );
        $dailySpecialDays = $this->getDailySpecialsDays($vendor->getSpecialDays(), $dateTime);
        $dailySchedules = $this->getDailySchedules($vendor->getSchedules(), $dateTime->format('N'));

        // special days have higher priority than normal schedules
        /** @var SpecialDay $specialDay */
        foreach ($dailySpecialDays as $day) {
            $openingTime = (int)str_replace(':', '', $day->getOpeningTime()) + $openingOffset;
            $closingTime = (int)str_replace(':', '', $day->getClosingTime()) + $closingOffset;
            if ($openingTime <= $time && $time <= $closingTime && $day->getOpeningType() === 'closed') {
                return false;
            }
            if ($openingTime <= $time && $time <= $closingTime && $day->getOpeningType() === 'delivering') {
                return true;
            }
        }

        // otherwise, start checking the schedules.
        /** @var Schedule $schedule */
        foreach ($dailySchedules as $schedule) {
            $openingTime = (int)str_replace(':', '', $schedule->getOpeningTime()) + $openingOffset;
            $closingTime = (int)str_replace(':', '', $schedule->getClosingTime()) + $closingOffset;
            if ($openingTime <= $time && $time <= $closingTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Vendor    $vendor
     * @param \DateTime $start
     * @param int       $openingOffset
     * @param int       $closingOffset
     *
     * @return ArrayCollection [ArrayCollection]
     */
    public function getNextFourDaysOpenings(Vendor $vendor, \DateTime $start, $openingOffset = 0, $closingOffset = 0)
    {
        $results = new ArrayCollection();

        $end = clone $start;
        $end->modify('+1 year');
        /** @var \DateTime $day */
        foreach (new \DatePeriod($start, new \DateInterval('P1D'), $end) as $day) {
            $dailyResults = new ArrayCollection();

            $day->setTime(0, 0, 0);
            $endDay = clone $day;
            $endDay->setTime(23, 59, 59);
            /** @var \DateTime $time */
            foreach (new \DatePeriod($day, new \DateInterval('PT30M'), $endDay) as $time) {
                if ($this->isVendorOpen($vendor, $time, $openingOffset, $closingOffset)) {
                    $dailyResults->add($time);
                }
            }

            if ($dailyResults->count() > 0) {
                $results->add($dailyResults);
            }

            if ($results->count() === 4) {
                break;
            }
        }

        return $results;
    }
}
