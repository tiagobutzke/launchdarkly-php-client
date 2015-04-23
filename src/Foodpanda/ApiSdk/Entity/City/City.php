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
     * @var Area
     */
    protected $main_area;

    public function __construct()
    {
        $this->main_area = new Area();
    }

    /**
     * @return Area
     */
    public function getMainArea()
    {
        return $this->main_area;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getIsTopCity()
    {
        return $this->is_top_city;
    }
}
