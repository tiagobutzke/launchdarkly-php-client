<?php

namespace Volo\EntityBundle\Entity\ProductCategoryVoucher;

use Volo\EntityBundle\Entity\DataObjectCollection;

class ProductCategoryVouchersCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return ProductCategoryVoucher::class;
    }
}
