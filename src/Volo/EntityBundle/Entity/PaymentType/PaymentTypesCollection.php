<?php

namespace Volo\EntityBundle\Entity\PaymentType;

use Volo\EntityBundle\Entity\DataObjectCollection;

class PaymentTypesCollection extends DataObjectCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getCollectionItemClass()
    {
        return PaymentType::class;
    }
}
