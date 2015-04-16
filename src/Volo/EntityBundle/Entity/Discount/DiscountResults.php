<?php

namespace Volo\EntityBundle\Entity\Discount;

use Volo\EntityBundle\Entity\DataResultObject;

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

    /**
     * @param DiscountsCollection $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
