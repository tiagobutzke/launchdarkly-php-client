<?php

namespace Volo\EntityBundle\Entity\City;

use Volo\EntityBundle\Entity\DataObjectCollection;

class CitiesCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return City::class;
    }
}
