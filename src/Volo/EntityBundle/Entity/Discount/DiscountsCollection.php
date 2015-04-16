<?php

namespace Volo\EntityBundle\Entity\Discount;

use Volo\EntityBundle\Entity\DataObjectCollection;

class DiscountsCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Discount::class;
    }
}
