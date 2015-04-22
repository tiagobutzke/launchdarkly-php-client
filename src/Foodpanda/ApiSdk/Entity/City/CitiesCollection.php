<?php

namespace Foodpanda\ApiSdk\Entity\City;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class CitiesCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return City::class;
    }
}
