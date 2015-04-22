<?php

namespace Volo\EntityBundle\Entity\VendorCart;

use Volo\EntityBundle\Entity\DataObjectCollection;

class ProductsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Product::class;
    }
}
