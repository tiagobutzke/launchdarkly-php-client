<?php

namespace Volo\EntityBundle\Entity\Vendor;

use Volo\EntityBundle\Entity\DataObjectCollection;

class VendorsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Vendor::class;
    }
}
