<?php

namespace Volo\FrontendBundle\Service;

use Doctrine\Common\Cache\Cache;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Schedule\Schedule;
use Foodpanda\ApiSdk\Entity\SpecialDay\SpecialDay;
use Foodpanda\ApiSdk\Entity\Schedule\SchedulesCollection;
use Foodpanda\ApiSdk\Entity\SpecialDay\SpecialDaysCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\TranslatorInterface;

class ScheduleService
{
    const CACHE_TTL = 82800; // 23 Hours

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var string
     */
    protected $timePickerDateFormat;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param Cache               $cache
     * @param TranslatorInterface $translator
     * @param string              $timePickerDateFormat
     */
    public function __construct(Cache $cache, TranslatorInterface $translator, $timePickerDateFormat)
    {
        $this->cache = $cache;
        $this->translator = $translator;
        $this->timePickerDateFormat = $timePickerDateFormat;
    }

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
        $fourDaysPeriod = $this->getNextFourDaysOpenings($vendor, $date, new \DateInterval('PT1M'));

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
        $hash = md5(serialize($vendor->getSchedules()) . serialize($vendor->getSpecialDays()) . serialize($collection));

        if (!$this->cache->contains($hash)) {
            $results = $collection->filter(function (\DateTime $time) use ($vendor) {
                if (in_array($time->format('H:i'), ['00:00', '23:59'])) {
                    return true;
                }

                $after  = clone $time;
                $before = clone $time;
                $after->modify('+1 minutes');
                $before->modify('-1 minutes');

                $closedBefore = !$this->isVendorOpen($vendor, $before);
                $closedAfter  = !$this->isVendorOpen($vendor, $after);

                return $closedBefore || $closedAfter;
            });

            $this->cache->save($hash, serialize($results), static::CACHE_TTL);
        }

        return unserialize($this->cache->fetch($hash));
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

        $isSpecialClose = false;
        $isSpecialOpen = false;
        // special days have higher priority than normal schedules
        /** @var SpecialDay $day */
        foreach ($dailySpecialDays as $day) {
            list($opening, $closing) = $this->getOpeningAndClosingTimes($time, $day, $openingOffset, $closingOffset);

            if ($opening <= $time && $time <= $closing && in_array($day->getOpeningType(), ['closed', 'unavailable'])) {
                $isSpecialClose = true;
            }
            if ($opening <= $time && $time <= $closing && $day->getOpeningType() === 'delivering') {
                $isSpecialOpen = true;
            }
        }

        if ($isSpecialClose) {
            return false;
        }
        if ($isSpecialOpen) {
            return true;
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
     * @param \DateInterval $interval
     * @param int       $openingOffset
     * @param int       $closingOffset
     *
     * @return ArrayCollection [ArrayCollection]
     */
    public function getNextFourDaysOpenings(
        Vendor $vendor,
        \DateTime $start,
        \DateInterval $interval,
        $openingOffset = 0,
        $closingOffset = 0
    ) {
        $time = clone $start;
        $time->setTime(0, 0);
        $schedules   = serialize($vendor->getSchedules());
        $specialDays = serialize($vendor->getSpecialDays());
        $hash = md5(
            $schedules . $specialDays . strval($openingOffset) . strval($closingOffset)
            . serialize($interval). serialize($time)
        );

        if (!$this->cache->contains($hash)) {
            $results = $this->calculateOpening($vendor, $time, $interval, $openingOffset, $closingOffset);

            $this->cache->save($hash, serialize($results), static::CACHE_TTL);
        }

        return unserialize($this->cache->fetch($hash));
    }

    /**
     * @param Vendor        $vendor
     * @param \DateTime     $start
     * @param \DateInterval $interval
     * @param int           $openingOffset
     * @param int           $closingOffset
     *
     * @return ArrayCollection
     */
    protected function calculateOpening(
        Vendor $vendor,
        \DateTime $start,
        \DateInterval $interval,
        $openingOffset,
        $closingOffset
    ) {
        $results = new ArrayCollection();

        $end = clone $start;
        $end->modify('+1 month');

        $this->filterSpecialDays($vendor, $end);
        /** @var \DateTime $day */
        foreach (new \DatePeriod($start, new \DateInterval('P1D'), $end) as $day) {
            $dailyResults = new ArrayCollection();

            $day->setTime(0, 0, 0);
            $endDay = clone $day;
            $endDay->setTime(23, 59, 59);
            /** @var \DateTime $time */
            foreach (new \DatePeriod($day, $interval, $endDay) as $time) {
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

    /**
     * @param ArrayCollection $times
     *
     * @return array
     */
    public function getTimePickerTimeValues(ArrayCollection $times)
    {
        $intl = \IntlDateFormatter::create(
            $this->translator->getLocale(),
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::SHORT
        );

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
        $openingPeriods = $this->getNextFourDaysOpenings(
            $vendor,
            $start,
            new \DateInterval('PT30M'),
            $vendor->getMinimumDeliveryTime(),
            -1
        );

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

        if ($firstPeriod->count() === 0 && !$this->isVendorOpen($vendor, $start)) {
            return $openingPeriods->slice(1, 3);
        } else {
            return new ArrayCollection([$firstPeriod] + $openingPeriods->slice(1, 2));
        }
    }

    /**
     * @param Vendor $vendor
     *
     * @return array
     */
    public function getTimePickerJsonValues(Vendor $vendor)
    {
        $days = [];
        foreach ($this->getTimePickerValues($vendor, new \DateTime()) as $day) {
            $times = [];

            if (($day->isEmpty() || $day->first()->format('Y-m-d') === date('Y-m-d')) && $this->isVendorOpen($vendor, new \DateTime())) {
                $times['now'] = $this->translator->trans('time_picker.now');
            }

            $dayKey = $day->isEmpty() ? new \DateTime : $day->first();
            $days[$dayKey->format('Y-m-d')] = [
                'text' => $this->formatOpeningDay($dayKey),
                'times'   => array_merge($times, $this->getTimePickerTimeValues($day)),
            ];
        }

        return $days;
    }

    /**
     * @param \DateTime $day
     *
     * @return bool|string
     */
    public function formatOpeningDay(\DateTime $day)
    {
        $formatter = \IntlDateFormatter::create(
            $this->translator->getLocale(),
            \IntlDateFormatter::GREGORIAN,
            \IntlDateFormatter::NONE
        );

        // @see http://userguide.icu-project.org/formatparse/datetime for formats
        $formatter->setPattern($this->timePickerDateFormat);

        return $formatter->format($day);
    }

    /**
     * @param Vendor $vendor
     * @param \DateTime $end
     */
    private function filterSpecialDays(Vendor $vendor, \DateTime $end)
    {
        foreach ($vendor->getSpecialDays() as $i => $specialDay) {
            if ($i > 10 || $end->getTimestamp() < strtotime($specialDay->getDate())) {
                $vendor->getSpecialDays()->removeElement($specialDay);
            }
        }
    }
}
