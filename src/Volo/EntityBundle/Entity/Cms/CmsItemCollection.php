<?php

namespace Volo\EntityBundle\Entity\Cms;

use Volo\EntityBundle\Entity\DataObjectCollection;

class CmsItemCollection extends DataObjectCollection
{
    /** @return string */
    protected function getCollectionItemClass()
    {
        return CmsItem::class;
    }
}
