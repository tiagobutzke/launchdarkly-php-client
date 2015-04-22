<?php

namespace Foodpanda\ApiSdk\Entity\Cms;

use Foodpanda\ApiSdk\Entity\DataObjectCollection;

class CmsItemCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return CmsItem::class;
    }
}
