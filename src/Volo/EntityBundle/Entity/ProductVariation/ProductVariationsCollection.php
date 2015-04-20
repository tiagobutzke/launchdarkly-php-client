<?php

namespace Volo\EntityBundle\Entity\ProductVariation;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
