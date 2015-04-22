<?php

namespace Foodpanda\ApiSdk\Entity\PaymentType;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

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
