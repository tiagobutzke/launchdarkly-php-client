<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ReorderProductsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return ReorderProduct::class;
    }
}
