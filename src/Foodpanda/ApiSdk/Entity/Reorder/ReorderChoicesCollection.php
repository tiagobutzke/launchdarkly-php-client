<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ReorderChoicesCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return ReorderChoice::class;
    }
}
