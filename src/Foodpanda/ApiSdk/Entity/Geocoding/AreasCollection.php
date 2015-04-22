<?php

namespace Foodpanda\ApiSdk\Entity\Geocoding;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class AreasCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Area::class;
    }
}
