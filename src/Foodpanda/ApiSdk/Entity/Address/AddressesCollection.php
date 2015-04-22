<?php

namespace Foodpanda\ApiSdk\Entity\Address;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class AddressesCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return Address::class;
    }
}
