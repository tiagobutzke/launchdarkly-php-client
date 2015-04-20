<?php

namespace Volo\EntityBundle\Entity\Cms;

use Volo\EntityBundle\Entity\DataResultObject;

class CmsResults extends DataResultObject
{
    public function __construct()
    {
        $this->items = new CmsItemCollection();
    }

    /**
     * @return CmsItemCollection|CmsItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param CmsItemCollection
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
