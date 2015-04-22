<?php

namespace Foodpanda\ApiSdk\Entity\Voucher;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class VouchersCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Voucher::class;
    }
}
