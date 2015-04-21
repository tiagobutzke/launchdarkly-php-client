<?php

namespace Volo\EntityBundle\Entity\VendorCart;

use Volo\EntityBundle\Entity\DataObjectCollection;

class VendorCartsCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return VendorCart::class;
    }
}
