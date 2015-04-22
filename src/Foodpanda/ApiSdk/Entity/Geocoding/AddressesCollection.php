<?php

namespace Foodpanda\ApiSdk\Entity\Geocoding;

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
