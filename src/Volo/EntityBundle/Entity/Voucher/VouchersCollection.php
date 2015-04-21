<?php

namespace Volo\EntityBundle\Entity\Voucher;

use Volo\EntityBundle\Entity\DataObjectCollection;

class VouchersCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return Voucher::class;
    }
}
