<?php

namespace Foodpanda\ApiSdk\Entity\Vendor;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
