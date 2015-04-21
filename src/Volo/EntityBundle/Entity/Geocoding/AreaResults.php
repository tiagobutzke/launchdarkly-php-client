<?php

namespace Volo\EntityBundle\Entity\Geocoding;

use Volo\EntityBundle\Entity\DataResultObject;

class AreaResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new AreasCollection();
    }

    /**
     * @return AreasCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param AreasCollection
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
