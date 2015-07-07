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
     * @param Vendor    $vendor
     * @param \DateTime $date
     *
     * @return ArrayCollection
     */
    public function getNextDayPeriods(Vendor $vendor, \DateTime $date)
    {
        /** @var ArrayCollection $fourDaysPeriod */
        /** @var ArrayCollection $openingAndClosingHours */
        $fourDaysPeriod = $this->getNextFourDaysOpenings($vendor, $date);

        if ($fourDaysPeriod->isEmpty()) {
            return new ArrayCollection();
        }

        $openingAndClosingHours = $this->filterOpeningAndClosingHours($vendor, $fourDaysPeriod->first());

        $periods = new ArrayCollection(array_chunk($openingAndClosingHours->toArray(), 2));

        $periods = $periods->filter(function (array $periods) use ($date) {
            return $periods[1] > $date;
        });

        if ($periods->count() === 0) {
            return new ArrayCollection(
                array_chunk($this->filterOpeningAndClosingHours($vendor, $fourDaysPeriod[1])->toArray(), 2)
            );
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
            $after->modify('+15 minutes');
            $before->modify('-15 minutes');

            $closedBefore = !$this->isVendorOpen($vendor, $before);
            $closedAfter  = !$this->isVendorOpen($vendor, $after);

            return $closedBefore || $closedAfter;
        });
    }

    /**
     * @param Vendor    $vendor
     * @param \DateTime $time
     * @param int       $openingOffset
     * @param int       $closingOffset
     *
     * @return bool
     */
    public function isVendorOpen(Vendor $vendor, \DateTime $time, $openingOffset = 0, $closingOffset = 0)
    {
        $dailySpecialDays = $this->getDailySpecialsDays($vendor->getSpecialDays(), $time);
        $dailySchedules = $this->getDailySchedules($vendor->getSchedules(), $time->format('N'));

        // special days have higher priority than normal schedules
        /** @var SpecialDay $day */
        foreach ($dailySpecialDays as $day) {
            list($opening, $closing) = $this->getOpeningAndClosingTimes($time, $day, $openingOffset, $closingOffset);
            
            if ($opening <= $time && $time <= $closing && $day->getOpeningType() === 'closed') {
                return false;
            }
            if ($opening <= $time && $time <= $closing && $day->getOpeningType() === 'delivering') {
                return true;
            }
        }

        // otherwise, start checking the schedules.
        /** @var Schedule $schedule */
        foreach ($dailySchedules as $schedule) {
            list($opening, $closing) = $this->getOpeningAndClosingTimes($time, $schedule, $openingOffset, $closingOffset);

            if ($opening <= $time && $time <= $closing) {
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

    /**
     * @param \DateTime           $time
     * @param int                 $openingOffset
     * @param int                 $closingOffset
     * @param SpecialDay|Schedule $schedule
     *
     * @return array
     */
    protected function getOpeningAndClosingTimes(\DateTime $time, $schedule, $openingOffset = 0, $closingOffset = 0)
    {
        $openingTime = clone $time;
        $closingTime = clone $time;
        call_user_func_array([$openingTime, 'setTime'], explode(':', $schedule->getOpeningTime()));
        $openingTime->modify(sprintf('+%s minutes', $openingOffset));
        call_user_func_array([$closingTime, 'setTime'], explode(':', $schedule->getClosingTime()));
        $closingTime->modify(sprintf('+%s minutes', $closingOffset));

        return array($openingTime, $closingTime);
    }
}
