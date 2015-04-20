<?php

namespace Volo\EntityBundle\Entity\Address;

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
