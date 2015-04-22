<?php

namespace Foodpanda\ApiSdk\Entity\Topping;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ToppingsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Topping::class;
    }
}
