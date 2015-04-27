<?php

namespace Foodpanda\ApiSdk\Entity\VendorCart;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class VendorCartProductsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return VendorCartProduct::class;
    }
}
