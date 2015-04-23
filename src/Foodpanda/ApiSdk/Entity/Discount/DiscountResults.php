<?php

namespace Foodpanda\ApiSdk\Entity\Discount;

use Foodpanda\ApiSdk\Entity\DataResultObject;

class DiscountResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new DiscountsCollection();
    }

    /**
     * @return DiscountsCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}
