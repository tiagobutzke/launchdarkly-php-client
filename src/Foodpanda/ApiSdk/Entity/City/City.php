<?php

namespace Foodpanda\ApiSdk\Entity\City;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Geocoding\Area;

class City extends DataObject
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $is_top_city;

    /**
     * @var array
     */
    protected $objectClasses = [
        'main_are' => Area::class,
    ];

    /**
     * @var Area
     */
    protected $main_area;

    /**
     * @return Area
     */
    public function getMainArea()
    {
        return $this->main_area;
    }

    /**
     * @param Area $main_area
     */
    public function setMainArea($main_area)
    {
        $this->main_area = $main_area;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getIsTopCity()
    {
        return $this->is_top_city;
    }

    /**
     * @param int $is_top_city
     */
    public function setIsTopCity($is_top_city)
    {
        $this->is_top_city = $is_top_city;
    }
}
