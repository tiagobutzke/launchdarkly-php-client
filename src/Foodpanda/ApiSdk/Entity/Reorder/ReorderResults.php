<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataResultObject;

class ReorderResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new ReordersCollection();
    }

    /**
     * @return ReordersCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}
