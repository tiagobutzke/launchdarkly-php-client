<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ReorderToppingsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return ReorderTopping::class;
    }
}
