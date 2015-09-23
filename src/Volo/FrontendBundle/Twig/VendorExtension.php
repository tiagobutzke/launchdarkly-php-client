<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\Cuisine\Cuisine;
use Foodpanda\ApiSdk\Entity\FoodCharacteristics\FoodCharacteristics;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Doctrine\Common\Collections\ArrayCollection;
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
            new \Twig_SimpleFunction('getVendorSearchData', array($this, 'getVendorSearchData')),
            new \Twig_SimpleFunction('getTimePickerJsonValues', array($this->scheduleService, 'getTimePickerJsonValues')),
        );
    }

    /**
     * Any values this method returns become searchable in the vendor listing page.
     *
     * @param Vendor $vendor
     *
     * @return array
     */
    public function getVendorSearchData(Vendor $vendor)
    {
        $data = [
            'name'                 => $vendor->getName(),
            'description'          => $vendor->getDescription(),
            'cuisines'             => [],
            'food_characteristics' => [],
        ];

        /** @var Cuisine $cuisine */
        foreach ($vendor->getCuisines() as $cuisine) {
            $data['cuisines'][] = $cuisine->getName();
        }

        /** @var FoodCharacteristics $characteristics */
        foreach ($vendor->getFoodCharacteristics() as $characteristics) {
            $data['food_characteristics'][] = $characteristics->getName();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vendor_extension';
    }
}
