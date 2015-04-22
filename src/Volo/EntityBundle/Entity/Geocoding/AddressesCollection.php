<?php

namespace Volo\EntityBundle\Entity\Geocoding;

use Volo\EntityBundle\Entity\DataObjectCollection;

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
