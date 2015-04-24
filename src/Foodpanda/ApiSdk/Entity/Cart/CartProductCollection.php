<?php

namespace Foodpanda\ApiSdk\Entity\Cart;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class CartProductCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return CartProduct::class;
    }
}
