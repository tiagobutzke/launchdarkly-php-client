<?php

namespace Volo\EntityBundle\Entity\Vendor;

use Volo\EntityBundle\Entity\DataResultObject;

class VendorResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new VendorsCollection();
    }

    /**
     * @return VendorsCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param VendorsCollection $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
