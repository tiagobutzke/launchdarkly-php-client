<?php

namespace Foodpanda\ApiSdk\Entity\ProductCategoryVoucher;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
