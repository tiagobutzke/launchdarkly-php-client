<?php

namespace Volo\EntityBundle\Entity\Geocoding;

use Volo\EntityBundle\Entity\DataResultObject;

class AddressResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new AddressesCollection();
    }

    /**
     * @return AddressesCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param AddressesCollection $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
