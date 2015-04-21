<?php

namespace Volo\EntityBundle\Entity\Topping;

use Volo\EntityBundle\Entity\DataObjectCollection;

class ToppingsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Topping::class;
    }
}
