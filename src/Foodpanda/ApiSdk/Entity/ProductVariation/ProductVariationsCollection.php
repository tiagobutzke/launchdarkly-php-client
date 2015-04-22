<?php

namespace Foodpanda\ApiSdk\Entity\ProductVariation;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class ProductVariationsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return ProductVariation::class;
    }
}
