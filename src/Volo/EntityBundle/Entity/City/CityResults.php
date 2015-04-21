<?php

namespace Volo\EntityBundle\Entity\City;

use Volo\EntityBundle\Entity\DataResultObject;

class CityResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new CitiesCollection();
    }

    /**
     * @return CitiesCollection|City[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param CitiesCollection
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
