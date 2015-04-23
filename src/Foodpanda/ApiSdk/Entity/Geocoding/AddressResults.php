<?php

namespace Foodpanda\ApiSdk\Entity\Geocoding;

use Foodpanda\ApiSdk\Entity\DataResultObject;

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
}
