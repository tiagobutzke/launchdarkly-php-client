<?php

namespace Volo\EntityBundle\Entity\Geocoding;

use Volo\EntityBundle\Entity\DataObjectCollection;

class AreasCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Area::class;
    }
}
