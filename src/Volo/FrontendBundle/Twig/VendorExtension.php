<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\Schedule\Schedule;
use Foodpanda\ApiSdk\Entity\Schedule\SchedulesCollection;
use Symfony\Component\Translation\TranslatorInterface;
use Volo\FrontendBundle\Service\OrderManagerService;

class VendorExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param string $locale
     * @param TranslatorInterface $translator
     */
    public function __construct($locale, TranslatorInterface $translator)
    {
        $this->locale = $locale;
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getClosingIn', array($this, 'getClosingIn')),
            new \Twig_SimpleFunction('getOpeningTime', array($this, 'getOpeningTime')),
            new \Twig_SimpleFunction('getClosingTime', array($this, 'getClosingTime')),
            new \Twig_SimpleFunction('getDeliveryDays', array($this, 'getDeliveryDays')),
            new \Twig_SimpleFunction('getNextOpeningHours', array($this, 'getNextOpeningHours')),
            new \Twig_SimpleFunction('getNextClosingHours', array($this, 'getNextClosingHours')),
            new \Twig_SimpleFunction('getNextOpeningWeekDayNumber', array($this, 'getNextOpeningWeekDayNumber')),
            new \Twig_SimpleFunction('getClosingHoursRange', array($this, 'getClosingHoursRange')),

            new \Twig_SimpleFunction('getOpeningPeriods', array($this, 'getOpeningPeriods')),
            new \Twig_SimpleFunction('getMinutesTilClosing', array($this, 'getMinutesTilClosing')),
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
     * @param SchedulesCollection $schedulesCollection
     * @param string $weekDay
     *
     * @return string
     */
    public function getClosingIn(SchedulesCollection $schedulesCollection, $weekDay = null)
    {
        $now = new \DateTime();
        $currentTime = $now->getTimestamp();

        $closingTime = $this->getClosingTime($schedulesCollection, $weekDay);
        $openingTime = $this->getOpeningTime($schedulesCollection, $weekDay);

        $noOpeningForThisDay = ($closingTime === null || $openingTime === null);

        if ($noOpeningForThisDay ||
            ($closingTime->getTimestamp() <= $currentTime) || ($openingTime->getTimestamp() > $currentTime)
        ) {
            return 0;
        }
        $diffToClosingTime = $now->diff($closingTime);

        return $diffToClosingTime->i + (60 * $diffToClosingTime->h);
    }

    /**
     * @param \DateTime $time
     * @param array $periods [[\DateTime $opening, \Datetime $closing], ..... [opening, closing]]
     *
     * @return array
     */
    public function getPeriodForTime(\DateTime $time, array $periods)
    {
        $timestamp = $time->getTimestamp();
        /** @var \DateTime[] $period */
        foreach ($periods as $period) {
            if ($timestamp >= $period[0]->getTimestamp() && $timestamp <= $period[1]->getTimestamp()) {
                return $period;
            }
        }

        return null;
    }

    /**
     * @param SchedulesCollection $schedulesCollection
     * @param string $weekDay
     *
     * @return string
     */
    public function getMinutesTilClosing(SchedulesCollection $schedulesCollection, $weekDay = null)
    {
        $openingPeriods = $this->getOpeningPeriods($schedulesCollection, $weekDay);
        $noOpeningForThisDay = (count($openingPeriods) === 0);

        $now = new \DateTime();
        $currentTimePeriod = $this->getPeriodForTime($now, $openingPeriods);
        if ($noOpeningForThisDay || (null === $currentTimePeriod)) {
            return 0;
        }
        $diffToClosingTime = $now->diff($currentTimePeriod[1]);

        return $diffToClosingTime->i + (60 * $diffToClosingTime->h);
    }

    /**
     * @param SchedulesCollection $schedulesCollection
     * @param int $weekDay
     *
     * @return array
     */
    public function getOpeningPeriods(SchedulesCollection $schedulesCollection, $weekDay = null)
    {
        $openingPeriods = $this->getTime($schedulesCollection, $weekDay, 'delivering', 'opening_time');
        $closingPeriods = $this->getTime($schedulesCollection, $weekDay, 'delivering', 'closing_time');

        $periods = [];
        if (count($openingPeriods) > 0) {
            foreach ($openingPeriods as $k => $period) {
                $date = $this->createDateTimeForDayOfTheWeek($weekDay);

                $periodStartDate = clone $date;
                $periodEndDate   = clone $date;

                $startPeriod = explode(':', $period);
                $endPeriod   = explode(':', $closingPeriods[$k]);

                $periodStartDate->setTime($startPeriod[0], $startPeriod[1]);
                $periodEndDate->setTime($endPeriod[0],     $endPeriod[1]);

                $periods[$k] = [$periodStartDate, $periodEndDate];
            }
        }

        return $periods;
    }

    /**
     * @param int $weekDay
     *
     * @return \DateTime
     */
    protected function createDateTimeForDayOfTheWeek($weekDay = null) {
        $todayOfTheWeek = (int) (new \DateTime())->format('N');
        if ($weekDay === null) {
            $weekDay = $todayOfTheWeek;
        }

        $timestamp = strtotime("+{$weekDay} day", strtotime('next Sunday'));
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $namedDayOfTheWeek = $dateTime->format('D');

        if ($todayOfTheWeek === (int) $weekDay) {
            $date = new \DateTime();
            $date->setTime(0,0,0);
        } else {
            $date = new \DateTime("next $namedDayOfTheWeek");
        }

        return $date;
    }

    /**
     * @param SchedulesCollection $schedulesCollection
     * @param int $weekDay
     *
     * @return \DateTime
     */
    public function getClosingTime(SchedulesCollection $schedulesCollection, $weekDay = null)
    {
        $closingPeriods = $this->getTime($schedulesCollection, $weekDay, 'delivering', 'closing_time');
        $closingAt = count($closingPeriods) === 0 ? null : $closingPeriods[0];

        return $closingAt === null ? null : \DateTime::createFromFormat('H:i', $closingAt);
    }

    /**
     * @param SchedulesCollection $schedulesCollection
     * @param int $weekDay
     *
     * @return \DateTime
     */
    public function getOpeningTime(SchedulesCollection $schedulesCollection, $weekDay = null)
    {
        $openingPeriods = $this->getTime($schedulesCollection, $weekDay, 'delivering', 'opening_time');
        $openingAt = count($openingPeriods) === 0 ? null : $openingPeriods[0];

        return $openingAt === null ? null : \DateTime::createFromFormat('H:i', $openingAt);
    }

    /**
     * @param SchedulesCollection $schedules
     * @param int $weekDay
     * @param string $openingType
     * @param string $field
     *
     * @return array
     */
    protected function getTime(SchedulesCollection $schedules, $weekDay, $openingType, $field)
    {
        $timeCollection = [];
        if ($weekDay === null) {
            $weekDay = (int)date('N');
        }

        /** @var Schedule $schedule */
        foreach ($schedules as $schedule) {
            if ((int)$weekDay === (int)$schedule->getWeekday() && $openingType === $schedule->getOpeningType()) {
                $isClosing = $field === 'closing_time';
                $timeCollection[] = $isClosing ? $schedule->getClosingTime() : $schedule->getOpeningTime();
            }
        }
        asort($timeCollection);

        return $timeCollection;
    }

    /**
     * @param SchedulesCollection $schedules
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    protected function isDeliveryPossibleOnThisDay(SchedulesCollection $schedules, \DateTime $dateTime)
    {
        $deliveryPossible = false;
        $dayOfTheWeek = $dateTime->format('N');
        $openingPeriods = $this->getOpeningPeriods($schedules, $dayOfTheWeek);

        if (count($openingPeriods) > 0) {
            /** @var \DateTime[] $period */
            foreach ($openingPeriods as $period) {
                /** @var \DateTime $closingTime */
                $closingTime = $period[1];
                if ($dateTime->getTimestamp() < $closingTime->getTimestamp()) {
                    $deliveryPossible = true;
                }
            }
        }

        return $deliveryPossible;
    }


    /**
     * @param \DateTime $dateTime
     *
     * @return int
     */
    protected function getSecondsInThisDay(\DateTime $dateTime)
    {
        $dateTimeToCheck = clone $dateTime;
        $secondsInThisDay = $dateTime->getTimestamp();
        $dateTimeToCheck->setTime(0, 0, 0);
        $secondsInThisDay -= $dateTimeToCheck->getTimestamp();

        return $secondsInThisDay;
    }

    /**
     * @param \DateTime $day
     */
    protected function incrementOneDay(\DateTime $day)
    {
        $day->add(new \DateInterval('P1D'));
        $day->setTime(0, 1, 0);
    }

    /**
     * @param SchedulesCollection $schedules
     * @param \DateTime $startDay
     *
     * @return \DateTime
     */
    protected function getNextOpeningDay(SchedulesCollection $schedules, \DateTime $startDay = null)
    {
        $dayToCheck = $startDay !== null ? clone $startDay : new \DateTime();

        if (!$this->isDeliveryPossibleOnThisDay($schedules, $dayToCheck)) {
            $this->incrementOneDay($dayToCheck);
        }

        for ($i = 0; $i < 7; $i++) {
            $weekDay = $dayToCheck->format('N');
            $openingPeriods = $this->getOpeningPeriods($schedules, $weekDay);

            if (count($openingPeriods) > 0) {
                return $dayToCheck;
            }
            $this->incrementOneDay($dayToCheck);
        }

        return null;
    }

    /**
     * @param SchedulesCollection $schedules
     * @param \DateTime $startDay
     *
     * @return int
     */
    public function getNextOpeningWeekDayNumber(SchedulesCollection $schedules, \DateTime $startDay = null)
    {
        $nextOpeningDay = $this->getNextOpeningDay($schedules, $startDay);

        return $nextOpeningDay === null ? null : $nextOpeningDay->format('N');
    }

    /**
     * @param SchedulesCollection $schedules
     *
     * @return int
     */
    public function getNextOpeningHours(SchedulesCollection $schedules)
    {
        $nextOpeningDay = $this->getNextOpeningWeekDayNumber($schedules);

        $periods = $this->getOpeningPeriods($schedules, $nextOpeningDay);
        $todayOfTheWeek = (new \DateTime())->format('N');
        if ($nextOpeningDay === $todayOfTheWeek) {
            $timeNow = $this->getTimeNow();
            foreach ($periods as $period) {
                /** @var \DateTime $closingTime */
                $closingTime = $period[1];
                if ($timeNow < $closingTime->getTimestamp()) {
                    return $period[0];
                }
            }
        }

        return $this->getOpeningTime($schedules, $nextOpeningDay);
    }

    /**
     * @param SchedulesCollection $schedules
     *
     * @return int
     */
    public function getNextClosingHours(SchedulesCollection $schedules)
    {
        $nextOpeningDay = $this->getNextOpeningWeekDayNumber($schedules);

        $periods = $this->getOpeningPeriods($schedules, $nextOpeningDay);
        $todayOfTheWeek = (new \DateTime())->format('N');
        if ($nextOpeningDay === $todayOfTheWeek) {
            $timeNow = $this->getTimeNow();
            foreach ($periods as $period) {
                /** @var \DateTime $closingTime */
                $closingTime = $period[1];
                if ($timeNow < $closingTime->getTimestamp()) {
                    return $closingTime;
                }
            }
        }

        return $this->getClosingTime($schedules, $nextOpeningDay);
    }

    /**
     * @param SchedulesCollection $schedules
     * @param int $numberOfDays
     * @param int $averageDeliveryTime
     * @param \DateTime $startDay
     *
     * @return \DateTime[]
     */
    public function getDeliveryDays(
        SchedulesCollection $schedules,
        $numberOfDays,
        $averageDeliveryTime,
        \DateTime $startDay = null
    ) {
        $dayToCheck = $startDay !== null ? clone $startDay : new \DateTime();
        $deliveryPossible = $this->isDeliveryPossibleOnThisDay($schedules, $dayToCheck);

        if (!$deliveryPossible) {
            $this->incrementOneDay($dayToCheck);
        }
        $openingDays = [];
        $attempts = 0;
        while ((count($openingDays) < $numberOfDays) && ($attempts < 7)) {
            $nextOpeningDay = $this->getNextOpeningDay($schedules, $dayToCheck);
            if ($this->canDeliver($nextOpeningDay, $schedules, $averageDeliveryTime)) {
                $openingDays[] = $nextOpeningDay;
            }

            // set the new startingDayToCheck to the new found day plus 1
            $dayToCheck->setTimestamp($nextOpeningDay->getTimestamp());
            $this->incrementOneDay($dayToCheck);
            $attempts++;
        }

        return $openingDays;
    }

    /**
     * @param \DateTime $day
     * @param SchedulesCollection $schedules
     * @param int $averageDeliveryTimeInMinutes
     * @return bool
     */
    protected function canDeliver(\DateTime $day, SchedulesCollection $schedules, $averageDeliveryTimeInMinutes)
    {
        $canDeliver = true;

        if ($this->isToday($day)) {
            $canDeliver = false;
            $openingPeriods = $this->getOpeningPeriods($schedules, $day->format('N'));
            $timeNow = $this->getTimeNow();
            foreach ($openingPeriods as $openingPeriod) {
                /** @var \DateTime $closingTime */
                $closingTime = $openingPeriod[1];
                if ($closingTime->getTimestamp() > $timeNow) {
                    $canDeliver = true;
                }
            }
        }

        return $canDeliver;
    }

    /**
     * @return int
     */
    protected function getTimeNow()
    {
        return (new \DateTime())->getTimestamp();
    }

    /**
     * @param \DateTime $day
     *
     * @return bool
     */
    protected function isToday(\DateTime $day)
    {
        $today = new \DateTime();

        return $today->format('Y-m-d') === $day->format('Y-m-d');
    }

    /**
     * This method takes a day (DateTime) and returns deliver ranges [[18:00, 19:00], [19:00, 20:00], [20:00, 21:30]]
     *
     * @param SchedulesCollection $schedules
     * @param \DateTime $day
     *
     * @param int $averageDeliveryTime average delivery time in minutes
     * @return array
     */
    public function getClosingHoursRange(SchedulesCollection $schedules, \DateTime $day, $averageDeliveryTime)
    {
        $averageDeliveryTimeInSeconds = $averageDeliveryTime * 60;

        $openingPeriods = $this->getOpeningPeriods($schedules, $day->format('N'));

        $allDeliveryRanges = [];

        foreach ($openingPeriods as $openingPeriod) {
            /** @var \DateTime $openingTime */
            $openingTime = $openingPeriod[0];
            /** @var \DateTime $closingTime */
            $closingTime = $openingPeriod[1];

            // for example if it open as 10:00 (am) then this number is 10 hrs * 3600 = 36,000 seconds
            $openingTimeInSecondsOfTheDay = $this->getSecondsInThisDay($openingTime);
            // for example if it closes as 22:00 (pm) then this number is 20 hrs * 3600 = 72,000 seconds
            $closingTimeInSecondsOfTheDay = $this->getSecondsInThisDay($closingTime);

            // This case covers restaurants that close after 24:00(00:00)
            if ($closingTimeInSecondsOfTheDay < $openingTimeInSecondsOfTheDay) {
                $closingTimeInSecondsOfTheDay += 86400;
            }

            $today = new \DateTime();
            $isOpen = false;

            $deliveryStartingTimeInSecondsOfTheDay = ($openingTimeInSecondsOfTheDay + $averageDeliveryTimeInSeconds);
            // in the following part we try to determine the 1st hour of deliver
            // such that if the day is today, we start from the next hour from the time now
            // otherwise(not today) we start from the normal opening hours
            if ($day->format('Y-m-d') === $today->format('Y-m-d')) {
                $unixTimestampOfNow = $today->getTimestamp();
                $today->setTime(0, 0, 0);
                $midnightTimestamp = $today->getTimestamp();
                // number of seconds that passed today from 00:00 til this moment e.g. if it's 7:30 am then (7.5 hrs * 3600)
                $secondsSinceTheBeginningOfToday = $unixTimestampOfNow - $midnightTimestamp + $averageDeliveryTimeInSeconds;
                $timeNow = $this->getTimeNow();
                $isOpen = ($openingTime->getTimestamp() < $timeNow) && ($closingTime->getTimestamp() > $timeNow);

                // if it's currently open, then the starting range is right now (this moment)
                if ($isOpen) {
                    $deliveryStartingTimeInSecondsOfTheDay = $unixTimestampOfNow - $midnightTimestamp;
                } elseif ($secondsSinceTheBeginningOfToday > $deliveryStartingTimeInSecondsOfTheDay) {
                    $deliveryStartingTimeInSecondsOfTheDay = $secondsSinceTheBeginningOfToday;
                }
            }

            $deliveryRanges = $this->getDeliveryRanges(
                $deliveryStartingTimeInSecondsOfTheDay,
                $closingTimeInSecondsOfTheDay,
                1800,
                $isOpen
            );

            $allDeliveryRanges = array_merge($allDeliveryRanges, $deliveryRanges);
        }

        return $allDeliveryRanges;
    }

    /**
     * @param int $actualStartingTimeInSecondsOfDay
     * @param int $closingTimeInSecondsOfDay
     * @param int $rangePeriodInSeconds
     * @param bool $isOpen
     *
     * @return array
     */
    protected function getDeliveryRanges(
        $actualStartingTimeInSecondsOfDay,
        $closingTimeInSecondsOfDay,
        $rangePeriodInSeconds,
        $isOpen
    ) {
        $dateTimeForFormatting = new \DateTime();

        $deliveryPairs = [];

        if ($isOpen) {
            $formattedValueRange = $this->translator->trans('time_picker.now');
            $rangeKey = OrderManagerService::ORDER_NOW_TIME_PICKER_IDENTIFIER;
            $deliveryPairs[$rangeKey] = $formattedValueRange;
        }
        // Here we start from the next whole (half hour) e.g. 14:30, 15:00, 15:30 ... etc
        $startingHour = ceil(2 * $actualStartingTimeInSecondsOfDay / 3600) / 2;
        $actualStartingTimeInSecondsOfDay = (int) ($startingHour * 3600);
        // we loop on the time hour by hour
        for ($i = $actualStartingTimeInSecondsOfDay; $i < $closingTimeInSecondsOfDay; $i += $rangePeriodInSeconds) {
            $startingTimeInHoursWithFraction = ($i / 3600);
            $endingTimeInHoursWithFraction = min($closingTimeInSecondsOfDay, ($i + $rangePeriodInSeconds)) / 3600;

            // calculating and formatting the range

            // Starting Time For The Slot
            $rangeStartingHours = floor($startingTimeInHoursWithFraction);
            $rangeStartingMinutes = ($startingTimeInHoursWithFraction - $rangeStartingHours) * 60;

            $dateTimeForFormatting->setTime($rangeStartingHours, $rangeStartingMinutes);
            $formattedRangeStartingTime = $this->formatTime($dateTimeForFormatting);

            // Ending Time For The Slot
            $rangeEndingHours = floor($endingTimeInHoursWithFraction);
            $rangeEndingMinutes = ($endingTimeInHoursWithFraction - $rangeEndingHours) * 60;

            $dateTimeForFormatting->setTime($rangeEndingHours, $rangeEndingMinutes);
            $formattedRangeEndingTime = $this->formatTime($dateTimeForFormatting);

            // Formatting the whole range for the slot
            $rangeKey = sprintf('%02d:%02d', $rangeStartingHours, $rangeStartingMinutes);

            $formattedValueRange = sprintf('%s - %s', $formattedRangeStartingTime, $formattedRangeEndingTime);

            $deliveryPairs[$rangeKey] = $formattedValueRange;
        }

        return $deliveryPairs;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    protected function formatTime(\DateTime $dateTime)
    {
        $formatter = \IntlDateFormatter::create($this->locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);

        return $formatter->format($dateTime);
    }
}
