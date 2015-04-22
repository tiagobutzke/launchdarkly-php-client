<?php

namespace Foodpanda\ApiSdk\Entity\Product;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
