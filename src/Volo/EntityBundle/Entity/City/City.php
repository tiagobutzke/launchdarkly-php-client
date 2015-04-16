<?php

namespace Volo\EntityBundle\Entity\City;

use Volo\EntityBundle\Entity\DataObject;

class City extends DataObject
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $is_top_city;

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
