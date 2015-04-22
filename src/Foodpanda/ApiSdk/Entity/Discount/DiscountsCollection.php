<?php

namespace Foodpanda\ApiSdk\Entity\Discount;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class DiscountsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Discount::class;
    }
}
