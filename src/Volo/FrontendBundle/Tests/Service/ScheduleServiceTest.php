<?php

namespace Volo\FrontendBundle\Tests\Service;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Volo\FrontendBundle\Service\ScheduleService;
use Volo\FrontendBundle\Tests\VoloTestCase;

class ScheduleServiceTest extends VoloTestCase
{
    /**
     * @var ScheduleService
     */
    protected $scheduleService;

    protected function setUp()
    {
        parent::setUp();

        // Random timezone
        date_default_timezone_set('Australia/Sydney');

        $this->scheduleService = new ScheduleService();
    }


    /**
     * @dataProvider isVendorOpenTestdataProvider
     *
     * @param Vendor    $vendor
     * @param \DateTime $dateTime
     * @param bool      $isOpen
     */
    public function testIsVendorOpen(Vendor $vendor, \DateTime $dateTime, $isOpen, $openingOffset = 0, $closingOffset = 0)
    {
        $this->assertEquals($isOpen, $this->scheduleService->isVendorOpen($vendor, $dateTime, $openingOffset, $closingOffset));
    }

    public function isVendorOpenTestdataProvider()
    {
        $testCases  = [];
        $serializer = new Serializer([new CustomNormalizer()], [new JsonEncoder()]);
        $now        = \DateTime::createFromFormat('Y-m-d H:i', '2015-07-01 16:00');
        $tomorrow   = clone $now;
        $yesterday  = clone $now;
        $tomorrow->modify('+1 day');
        $yesterday->modify('+1 day');

        $testCases[] = [new Vendor(), $now, false];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, true];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '16:30', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '15:30']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'pickup', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:30', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, true, 15];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:30', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, true, 30];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:30', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false, 45];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:30', 'closing_time' => '16:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false, 0, -1];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:30', 'closing_time' => '16:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, true, 0, 1];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:30', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, true, 29];

        $data = ['schedules' => [['weekday' => (int)$tomorrow->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = ['schedules' => [['weekday' => (int)$yesterday->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '16:30', 'closing_time' => '17:00']],
            'special_days' => [['date' => $now->format('Y-m-d'), 'opening_type' => 'delivering', 'opening_time' => '16:00', 'closing_time' => '16:30']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, true];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '16:30', 'closing_time' => '17:00']],
            'special_days' => [['date' => $now->format('Y-m-d'), 'opening_type' => 'open', 'opening_time' => '16:00', 'closing_time' => '16:30']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '16:30', 'closing_time' => '17:00']],
            'special_days' => [['date' => $now->format('Y-m-d'), 'opening_type' => 'open', 'opening_time' => '15:00', 'closing_time' => '15:30']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '16:30', 'closing_time' => '17:00']],
            'special_days' => [['date' => $tomorrow->format('Y-m-d'), 'opening_type' => 'open', 'opening_time' => '15:00', 'closing_time' => '15:30']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '16:30', 'closing_time' => '17:00']],
            'special_days' => [['date' => $yesterday->format('Y-m-d'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '15:30']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']],
            'special_days' => [['date' => $now->format('Y-m-d'), 'opening_type' => 'closed', 'opening_time' => '15:00', 'closing_time' => '17:00']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, false];

        return $testCases;
    }

    /**
     * @dataProvider getNextFourOpeningDaysTestdataProvider
     *
     * @param Vendor    $vendor
     * @param \DateTime $dateTime
     * @param int       $nbDays
     */
    public function testGetNextFourOpeningDays($vendor, $dateTime, $nbDays)
    {
        $this->assertEquals($nbDays, $this->scheduleService->getNextFourDaysOpenings($vendor, $dateTime, new \DateInterval('PT30M'))->count());
    }
    
    public function getNextFourOpeningDaysTestdataProvider()
    {
        $testCases  = [];
        $serializer = new Serializer([new CustomNormalizer()], [new JsonEncoder()]);
        $now        = \DateTime::createFromFormat('Y-m-d H:i', '2015-07-01 16:00');

        $testCases[] = [new Vendor(), $now, 0];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, 4];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, 4];

        $data = ['schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '15:30']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, 4];

        $data = [
            'schedules' => [['weekday' => (int)$now->format('N'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '15:30']],
            'special_days' => [['date' => $now->format('Y-m-d'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]
        ];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, 4];

        $data = ['special_days' => [['date' => $now->format('Y-m-d'), 'opening_type' => 'delivering', 'opening_time' => '15:00', 'closing_time' => '17:00']]];
        $testCases[] = [$serializer->denormalizeVendor($data), $now, 1];

        return $testCases;
    }
}
