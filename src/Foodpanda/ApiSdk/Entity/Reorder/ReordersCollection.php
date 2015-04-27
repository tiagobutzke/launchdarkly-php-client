<?php

namespace Foodpanda\ApiSdk\Entity\Reorder;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ReordersCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Reorder::class;
    }
}
